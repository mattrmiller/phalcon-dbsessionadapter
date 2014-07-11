<?php

/**
 * Class DbSessionAdapter
 */
class DbSessionAdapter extends \Phalcon\Session\Adapter implements \Phalcon\Session\AdapterInterface
{
	/**
	 * Is destroyed
	 * @var bool
	 */
	protected $_bDestroyed = false;

	/**
	 * Construct
	 */
	public function __construct($aOptions = null)
	{
		// Parent
		parent::__construct($aOptions);

		// Setup settings
		ini_set('session.gc_maxlifetime', $aOptions['lifetime']);
		ini_set('session.cookie_lifetime', $aOptions['lifetime']);
		ini_set('session.name', $aOptions['name']);
		ini_set('session.domain', $aOptions['domain']);
		ini_set('session.cookie_secure', $aOptions['secure']);
		ini_set('session.use_cookies', $aOptions['use_cookies']);
		ini_set('session.hash_function', $aOptions['hash']);

		// Set session handler
		session_set_save_handler(
			array($this, 'open'),
			array($this, 'close'),
			array($this, 'read'),
			array($this, 'write'),
			array($this, 'destroy'),
			array($this, 'gc')
		);

		// Start session
		$this->start();
	}

	/**
	 * Get session
	 * @param $sSessionId
	 * @return null|\Phalcon\Mvc\Model|\Phalcon\Mvc\ModelInterface
	 */
	private function _getSession($sSessionId)
	{
		// Find session
        $oSession = Sessions::query()
	        ->where('uuid = :uuid:')
		    ->bind(array(
			    'uuid' => $sSessionId
		    ))
		    ->limit(1)
		    ->execute()
	        ->getFirst();
	    if (empty($oSession)) {
		    return null;
	    }

        return $oSession;
	}

	/**
	 * Open
	 * @return bool
	 */
	public function open()
	{
		return true;
	}

	/**
	 * Close
	 * @return bool
	 */
	public function close()
	{
		return false;
	}

	/**
	 * Read
	 * @param $sSessionId
	 * @return string
	 */
	public function read($sSessionId)
	{
		// Find session
        $oSession = $this->_getSession($sSessionId);
	    if (empty($oSession)) {
		    return '';
	    }

        return $oSession->data;
	}

	/**
	 * Write
	 * @param $sSessionId
	 * @param $sData
	 * @return bool
	 */
	public function write($sSessionId, $sData)
	{
		// Destroyed?
		if ($this->_bDestroyed) {
			return false;
		}

		// Find session
        $oSession = $this->_getSession($sSessionId);
	    if (empty($oSession)) {
		    $oSession = new Sessions();
		    $oSession->uuid = $sSessionId;
	    }

		// Set data
		$oSession->data = $sData;

		// Save
		return $oSession->save();
	}


	/**
	 * Destroy
	 * @param null $sSessionId
	 * @return bool
	 */
	public function destroy($sSessionId = null)
	{
		// Started or not?
		if (!$this->isStarted() || $this->_bDestroyed) {
			return true;
		}

		// Get session Id if null
		if (is_null($sSessionId)) {
			$sSessionId = $this->getId();
		}

		// Find session
        $oSession = $this->_getSession($sSessionId);
	    if (!empty($oSession)) {
		    $this->_bDestroyed = true;
		    $oSession->delete();
	    }

		// Regenerate Id
		session_regenerate_id();

		return true;
	}

	/**
	 * Garbage collector
	 * @param $iMaxLifetime
	 * @return mixed
	 */
	public function gc($iMaxLifetime)
	{
		// Find sessions
        $oSessions = Sessions::query()
	        ->where('UNIX_TIMESTAMP(date_modified) <= :maxlife:')
		    ->bind(array(
			    'maxlife' => $iMaxLifetime
		    ))
		    ->execute();

		// Delete
		foreach ($oSessions as $oSession)
		{
			$oSession->delete();
		}
	}
}
