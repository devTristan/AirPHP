<?php
$fields = array(
	'id' => field::number(4294967295),
	'username' => field::varchar(128),
	'password' => field::password(),
	'name' => field::varchar(128),
	'join_date' => field::timestamp_on_insert(),
	'last_visit' => field::timestamp()
	);
$index = array(
	index::primary('id'),
	index::unique('username'),
	index::index('join_date'),
	index::index('last_visit')
	);
$defaults = array(
	'name' => 'unnamed'
	);
$identifier = 'id';

class model_users extends model {
	public function validate_username($username)
		{
		$errors = array();
		$new = str::allow($username,str::alphanumeric);
		if ($new !== $username)
			{
			$errors[] = 'Usernames must only contain alphanumeric characters.';
			}
		if (strlen($username) < 3 || strlen($username) > 128)
			{
			$errors[] = 'Usernames must be between 3 and 128 characters long.';
			}
		return $errors;
		}
}
