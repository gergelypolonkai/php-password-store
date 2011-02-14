<?php
if (!class_exists('PWSdbhException'))
{
	define('PWSDBH_SUCCESS',    0);
	define('PWSDBH_NOMATCH',    1);
	define('PWSDBH_BADOLD',     2);
	define('PWSDBH_WEAKPW',     3);
	define('PWSDBH_BADUSER',    4);
	define('PWSDBH_NONEWPW',    5);
	define('PWSDBH_BADACCOUNT', 6);
	class PWSdbhException extends Exception { }
}

if (!class_exists('PWSdb'))
{
	class PWSdb extends PDO
	{
		var $userList;
		var $userIsAdmin;
		var $userUserGroups;
		var $userPasswordGroups;

		var $key;
		var $alg;
		var $mode;

		function __construct($dsn, $username = null, $password = null, $driver_options = null)
		{
			$this->userList = array();
			$this->userIsAdmin = array();
			$this->userUserGroups = array();
			$this->userPasswordGroups = array();
			$this->key = null;
			$this->alg = MCRYPT_RIJNDAEL_256;
			$this->mode = MCRYPT_MODE_ECB;

			if ($username !== null)
			{
				if ($password !== null)
				{
					if ($driver_options !== null)
					{
						parent::__construct($dsn, $username, $password, $driver_options);
					}
					else
					{
						parent::__construct($dsn, $username, $password);
					}
				}
				else
				{
					parent::__construct($dsn, $username);
				}
			}
			else
			{
				parent::__construct($dsn);
			}
		}

		function setKey($key)
		{
			$this->key = $key;
		}

		function userExists($username)
		{
			if (in_array($username, $this->userList))
			{
				return true;
			}
			$sth = $this->prepare('SELECT id FROM users WHERE username = ?');
			$sth->execute(array($username));
			if ($sth->fetch())
			{
				$this->userList[] = $username;
				return true;
			}

			return false;
		}

		function isAdmin($username)
		{
			if (!array_key_exists($username, $this->userIsAdmin))
			{
				$userIsAdmin[$username] = false;

				$sth = $this->prepare('SELECT administrator FROM users WHERE username = ?');
				$sth->execute(array($username));
				if ($row = $sth->fetch())
				{
					$this->userIsAdmin[$username] = ($row['administrator'] == 1);
				}
			}

			return $this->userIsAdmin[$username];
		}

		function userUsergroups($username)
		{
			if (!array_key_exists($username, $this->userUserGroups))
			{
				$this->userUserGroups[$username] = array();

				$sth = $this->prepare('SELECT groups FROM users WHERE username = ?');
				$sth->execute(array($username));
				if ($row = $sth->fetch())
				{
					$this->userUserGroups[$username] = explode(':', $row['groups']);
				}
			}

			return $this->userUserGroups[$username];
		}

		function userPasswordgroups($username)
		{
			if (!array_key_exists($username, $this->userPasswordGroups))
			{
				$this->userPasswordGroups[$username] = array();

				if ($this->isAdmin($username))
				{
					$query = 'SELECT groupname || \'+\' AS permissions FROM passwordgroups';
				}
				else
				{
					$query = 'SELECT permissions FROM usergroups WHERE groupname IN (\'' . join("', '", $this->userUsergroups($username)) . '\')';
				}
				$sth = $this->prepare($query);
				$sth->execute();
				while ($row = $sth->fetch())
				{
					$pwgs = explode(':', $row['permissions']);
					foreach ($pwgs as $pwg)
					{
						$writable = false;
						if (substr($pwg, -1) == '+')
						{
							$pwg = substr($pwg, 0, -1);
							$writable = true;
						}

						if (!array_key_exists($pwg, $this->userPasswordGroups[$username]))
						{
							$this->userPasswordGroups[$username][$pwg] = ($writable) ? 'rw' : 'r';
						}
						else if ($writable)
						{
							$this->userPasswordGroups[$username][$pwg] = 'rw';
						}
					}
				}
			}

			return $this->userPasswordGroups[$username];
		}

		function changePassword($username, $oldpw, $newpw1, $newpw2)
		{
			if ($newpw1 != $newpw2)
			{
				throw new PWSdbhException('New passwords don\'t match', PWSDBH_NOMATCH);
			}

			if ($newpw1 == '')
			{
				throw new PWSdbhException('No new password is provided!', PWSDBH_NONEWPW);
			}

			if ($this->weakPassword($newpw1))
			{
				throw new PWSdhbException('New password is too weak', PWSDBH_WEAKPW);
			}

			$sth = $this->prepare('SELECT username, password FROM users WHERE username = ?');
			if ($sth->execute(array($username)))
			{
				if ($row = $sth->fetch())
				{
					if (crypt($oldpw, $row['password']) != $row['password'])
					{
						throw new PWSdbhException('Old password doesn\'t match!', PWSDBH_BADOLD);
					}
					else
					{
						$update_sth = $this->prepare('UPDATE users SET password = ? WHERE username = ?');
						if ($update_sth->execute(array(crypt($newpw1), $username)))
						{
							return true;
						}
					}
				}
				else
				{
					throw new PWSdbhException('No such user!', PWSDBH_BADUSER);
				}
			}
		}

		function weakPassword($password)
		{
			if (!preg_match('/[0-9]/', $password))
			{
				return true;
			}
			if (!preg_match('/[a-z]/', $password))
			{
				return true;
			}
			if (!preg_match('/[A-Z]/', $password))
			{
				return true;
			}

			return false;
		}

		function passwordgroupAccessible($groupname, $username, $forwrite = false)
		{
			if ($this->isAdmin($username))
			{
				return true;
			}

			$accessibleGroups = $this->userPasswordgroups($username);

			return (array_key_exists($groupname, $accessibleGroups) && (!$forwrite || $accessibleGroups[$groupname] == 'rw'));
		}

		function getPasswordgroupData($groupname)
		{
			$sth = $this->prepare('SELECT id, groupname, description FROM passwordgroups WHERE groupname = ?');
			$sth->execute(array($_POST['name']));
			if ($row = $sth->fetch())
			{
				return $row;
			}

			return null;
		}

		function passwordAccessible($passwordId, $username, $forwrite = false)
		{
			if ($this->isAdmin($username))
			{
				return true;
			}

			$sth = $this->prepare('SELECT id, groups FROM passwords WHERE id = ?');
			$sth->execute(array($passwordId));
			if ($row = $sth->fetch())
			{
				$groups = explode(':', $row['groups']);
				foreach ($groups as $group)
				{
					if ($this->passwordgroupAccessible($group, $username, $forwrite))
					{
						return true;
					}
				}
			}
			
			return false;
		}

		function getPasswordData($passwordId)
		{
			$sth = $this->prepare('SELECT id, short, long, username, password, additional, groups FROM passwords WHERE id = ?');
			$sth->execute(array($passwordId));
			if ($row = $sth->fetch())
			{
				if (substr($row['password'], 0, 7) == '{CLEAR}')
				{
					$row['password'] = substr($row['password'], 7);
					$this->updatePassword($passwordId, $row['password']);
				}
				else
				{
					$row['password'] = $this->decryptPassword($row['password']);
				}
				$this->updatePasswordAccess($passwordId);

				return $row;
			}

			return array();
		}

		function updatePassword($passwordId, $newPassword, $username = null)
		{
			if ($username === null)
			{
				$sth = $this->prepare('UPDATE passwords SET password = ? WHERE id = ?');
				$params = array($this->encryptPassword($newPassword), $passwordId);
			}
			else
			{
				$sth = $this->prepare('UPDATE passwords SET password = ?, modifiedby = ?, modifiedat = datetime(\'now\') WHERE id = ?');
				$params = array($this->encryptPassword($newPassword), $username, $passwordId);
			}
			$sth->execute();
		}

		function updatePasswordAccess($passwordId)
		{
			$sth = $this->prepare('UPDATE passwords SET lastaccess = datetime(\'now\') WHERE id = ?');
			$sth->execute(array($passwordId));
		}

		function encryptPassword($password)
		{
			return base64_encode($this->_encryptPassword($password));
		}

		function decryptPassword($password)
		{
			return $this->_decryptPassword(base64_decode($password));
		}

		private function _encryptPassword($password)
		{
			$cipher = mcrypt_module_open($this->alg, '', 'ecb', '');
			$iv_size = mcrypt_get_iv_size($this->alg, $this->mode);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);
			mcrypt_generic_init($cipher, $this->key, $iv);
			$enc = mcrypt_generic($cipher, $password);
			mcrypt_generic_deinit($cipher);
			
			return $enc;
		}

		private function _decryptPassword($password)
		{
			$cipher = mcrypt_module_open($this->alg, '', 'ecb', '');
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);
			mcrypt_generic_init($cipher, $this->key, $iv);
			$dec = mdecrypt_generic($cipher, $password);
			mcrypt_generic_deinit($cipher);

			for ($i = 0; $i < strlen($dec); $i++)
			{
				if (ord(substr($dec, $i, 1)) == 0)
					break;
			}

			if ($i < strlen($dec))
			{
				$dec = substr($dec, 0, $i);
			}

			return $dec;
		}

		function authUser($username, $password)
		{
			$sth = $this->prepare('SELECT username, password FROM users WHERE username = ?');
			$sth->execute(array($username));
			if ($row = $sth->fetch())
			{
				if (crypt($password, $row['password']) == $row['password'])
				{
					$this->updateUserRecord($username);
					return true;
				}
			}

			throw new PWSdbhException('Bad username or password!', PWSDBH_BADACCOUNT);

			return false;
		}

		function updateUserRecord($username)
		{
			$sth = $this->prepare('UPDATE users SET lastlogin = datetime(\'now\') WHERE username = ?');
			$sth->execute(array($username));
		}

		function getAllPasswords($pwgroup)
		{
			$sth = $this->prepare('SELECT id, short FROM passwords WHERE groups LIKE ? OR groups LIKE ? OR groups LIKE ? OR groups = ?');
			$sth->execute(array('%:' . $pwgroup . ':%', '%:' . $pwgroup, $pwgroup . ':%', $pwgroup));
			return $sth->fetchAll();
		}

		function findPasswords($query)
		{
			$sth = $this->prepare('SELECT id, short, groups FROM passwords WHERE short LIKE :querytext OR long LIKE :querytext ESCAPE \'~\' ORDER BY short');
			$sth->execute(array(':querytext' => '%' . str_replace(array('%', '_'), array('~%', '~_'), $query) . '%'));
			return $sth->fetchAll();
		}
	}
}

if (!isset($pwsdbhs))
{
	$pwsdbhs = array();
}

if (!function_exists('pwsdbh'))
{
	function pwsdbh($dsn, $username = null, $password = null, $driver_options = null)
	{
		global $pwsdbhs;

		$key = $dsn . '|' . $username . '|' . $password . '|' . $driver_options;

		if (array_key_exists($key, $pwsdbhs))
		{
			if ($pwsdbhs[$key] === null)
			{
				$pwsdbhs[$key] = new PWSdb($dsn, $username, $password, $driver_options);
			}
		}
		else
		{
			$pwsdbhs[$key] = new PWSdb($dsn, $username, $password, $driver_options);
		}
		
		return $pwsdbhs[$key];
	}
}
