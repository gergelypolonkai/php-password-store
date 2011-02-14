#! /bin/sh

# Create a file suitable to use as an encrypted block device for password store.
#
# Required tools are dd, mkfs of the chosen filesystem, losetup and the chosen
# encryption module

# Defaults:
#  Output file: pwstore.blk
#  Filesystem:  ext3
#  Encryption:  twofish

output_file=pwstore.blk
size=128
size_unit=M
fs_type=ext3
enc_module=twofish

if [ x"$UID" != "x0" ]
then
	echo "This script must be run as root!"
	exit 1
fi

loop_device=""

for dev in `ls /dev/loop*`
do
	if ! losetup $dev &> /dev/null
	then
		loop_device=$dev
		break
	fi
done

if [ -z $loop_device ]
then
	echo "Can not find a free loop device!"
	exit 1
else
	echo "Will use loop device $loop_device"
fi

if [ -f "$output_file" ]
then
	echo "The output file ($output_file) already exists!"
	exit 1
fi

# Create the file that will serve as a block device
echo "Creating file $output_file"
dd if=/dev/zero of="$output_file" bs="1$size_unit" count="$size"

if [ $? -ne 0 ]
then
	echo "Can not create output file."
	exit 1
fi

first_run=1
enc_pw1=1
enc_pw2=2

while [ x"$enc_pw1" != x"$enc_pw2" ]
do
	if [ $first_run == 1 ]
	then
		first_run=0
	else
		echo "The two passwords do not match!"
		echo
	fi
	read -s -p "Password to encrypt the block device file with: " enc_pw1
	echo
	read -s -p "Repeat password: " enc_pw2
	echo
done

lsmod | grep '^cryptoloop ' &> /dev/null
if [ $? -ne 0 ]
then
	echo "Loading the cryptoloop module"
	modprobe cryptoloop
fi

echo "Setting up encrypted loop device $loop_device with $enc_module as an encryption module."
echo $enc_pw1 | losetup -p 0 -e "$enc_module" "$loop_device" "$output_file"

losetup "$loop_device"

"mkfs.$fs_type" "$loop_device"

sync

losetup -d "$loop_device"

echo "Your encrypted password store partition is ready, encrypted with the passphrase"
echo "you provided. To mount it, use the mountencpart.sh script distributed with WPM"
echo "in the scripts/ directory. Be warned that if you forget your passphrase, you"
echo "won't be able to retrieve your passwords!"
echo

exit 0

