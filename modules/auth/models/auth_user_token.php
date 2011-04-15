<?php defined('SYSPATH') OR die('No direct access allowed.');

class Auth_User_Token_Model extends ORM {

	// Relationships
	protected $belongs_to = array('user');

	// Current timestamp
	protected $now;

	/**
	 * Handles garbage collection and deleting of expired objects.
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		// Set the now, we use this a lot
		$this->now = time();

		if (mt_rand(1, 100) === 1)
		{
			// Do garbage collection
			$this->delete_expired();
		}

		if ($this->expires < $this->now)
		{
			// This object has expired
			$this->delete();
		}
	}

	/**
	 * Overload saving to set the created time and to create a new token
	 * when the object is saved.
	 */
	public function save()
	{
		if ($this->loaded === FALSE)
		{
			// Set the created time, token, and hash of the user agent
			$this->created = $this->now;
			$this->user_agent = sha1(Kohana::$user_agent);
		}

		// Create a new token each time the token is saved
		$this->token = $this->create_token();

		return parent::save();
	}

	/**
	 * Deletes all expired tokens.
	 *
	 * @return  void
	 */
	public function delete_expired()
	{
		// Delete all expired tokens
		$this->db->where('expires <', $this->now)->delete($this->table_name);

		return $this;
	}

	/**
	 * Finds a new unique token, using a loop to make sure that the token does
	 * not already exist in the database. This could potentially become an
	 * infinite loop, but the chances of that happening are very unlikely.
	 *
	 * @return  string
	 */
	protected function create_token()
	{
		while (TRUE)
		{
			// Create a random token
			$token = text::random('alnum', 32);

			// Make sure the token does not already exist
			if ($this->db->select('id')->where('token', $token)->get($this->table_name)->count() === 0)
			{
				// A unique token has been found
				return $token;
			}
		}
	}

	/**
	 * Allows loading by token string.
	 */
	public function unique_key($id)
	{
		if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
		{
			return 'token';
		}

		return parent::unique_key($id);
	}

} // End Auth User Token Model