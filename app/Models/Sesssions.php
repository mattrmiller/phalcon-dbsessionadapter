<?php

/**
 * Class Sessions
 */
class Sessions extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $uuid;

    /**
     *
     * @var string
     */
    public $data;

    /**
     *
     * @var integer
     */
    public $created_at;

    /**
     *
     * @var integer
     */
    public $modified_at;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'uuid' => 'uuid', 
            'data' => 'data', 
            'created_at' => 'created_at', 
            'modified_at' => 'modified_at'
        );
    }

	/**
	 * Initialize
	 */
	public function initialize()
    {
	    // Dynamic updates
	    $this->useDynamicUpdate(true);

	    // Skip attributes
        $this->skipAttributesOnCreate(array('created_at', 'modified_at'));
	    $this->skipAttributesOnUpdate(array('uuid'));
    }
}
