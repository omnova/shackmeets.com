<?php defined('SYSPATH') OR die('No Direct Script Access');

abstract class Model_Base extends Model
{
  protected $table_name = null;
  protected $primary_key_col = null;
  protected $auto_key = true;
  protected $columns = null;
  
  protected $original_data = array();
  private $_saved = false;
  private $_loaded = false;
  
  public $metadata = array();
  
  function __construct($id = null)
	{		
		$this->clear();

		if ($id != null)
			return $this->load_by_id($id);
    else
      return $this;
	}
  
  
  // Generic Load
	public function load($keys = array(), $order_by = array())
	{
    $query = DB::select_array($this->columns)->from($this->table_name);
    
    if (count($keys) > 0)
		{
      $first_where = true;
    
			foreach ($keys as $key => $value)
			{
        if ($first_where)
        {
          $query->where($key, '=', $value);
          $first_where = false;
        }
        else
        {
          $query->and_where($key, '=', $value);
        }
			}
		}

    // Temp implementation
    foreach ($order_by as $value)
    {        
      $query->order_by($value);
    }

    return $this->build_query_result($query); 
	}
  
  // Loads data for the object with provided ID
	public function load_by_id($id, $load_into_this = true) 
	{
    // create a new instance of this model to load into. recursion ftw!
    if (!$load_into_this)
      return model::factory($this->table_name)->load($username);
  
    $result = $this->load(array($this->primary_key_col => $id));
    
    if (count($result) == 1)
      return $result[0];
    else
      return null;
	}
  
  
  public function build_query_result($query)
  {
    $query_result = $query->execute()->as_array();
    
    $results = array();
    
    foreach ($query_result as $row)
    {
      $item = Model::factory($this->table_name);
    
      $item->populate_from_array($row);
      $item->clean();
      $item->_loaded = true;
    
      $results[] = $item;
    }
    
    return $results;
  }  
  
  public function populate_from_array($data)
  {
    foreach ($data as $field => $value)
    {
      $this->$field = $value;
    }
    
    $this->clean();
  }
  
  // Save
  
	public function save()
	{
		// Don't save if nothing changed
		if (!$this->is_dirty())
		{
			$this->_saved = true;
			return true;
		}

		if ($this->pk_value() != null && $this->auto_key)
		{
			return $this->update();
		}
		else
		{
			return $this->insert();
		}
	}
  
  // Insert
  
	public function insert()
	{
    $cols = array();
    $data = array();
    
    foreach ($this->columns as $column)
    {
      if (!($this->auto_key && $column == $this->primary_key_col) && $this->$column != $this->original_data[$column])
      {
        $cols[$column] = $column;
        $data[$column] = $this->$column;        
      }
    }
  
    $query = DB::insert($this->table_name, $cols)->values($data);
    
    $result = $query->execute();
    $insert_id = $result[0];
    $row_count = $result[1];
    
    if ($this->auto_key)
    {
      $primary_key = $this->primary_key_col;
    
      $this->$primary_key = $insert_id;
    }
  
    $this->clean();
    $this->_saved = true;
    $this->_loaded = false;
  
    return $this;
	}
  
  // Update
  
  public function update()
	{
    $data = array();
    
    foreach ($this->columns as $column)
    {
      if ($column != $this->primary_key_col && $this->$column != $this->original_data[$column])
      {
        $data[$column] = $this->$column;        
      }
    }
    
    $rows_affected = 0;
    
    if (count($data) > 0)
    {  
      $query = DB::update($this->table_name)->set($data)->where($this->primary_key_col, '=', $this->pk_value());
      
      $rows_affected = $query->execute();
    }
    
    if ($rows_affected > 0 || count($data) == 0)
    {  
      $this->clean();
      $this->_saved = true;
      $this->_loaded = false;  
    }
    else
    {
      $this->_saved = false;
      $this->_loaded = false;  
    }
  
    return $this;
	}
  
  // Delete
  
	public function delete()
	{    
    $query = DB::delete($this->table_name)->where($this->primary_key_col, '=', $this->pk_value());

		$rows_affected = $query->execute();

    if ($rows_affected > 0)
    {
      $this->clean();
    }
	}
  
  // Helpers
  
  public function pk_value()
  {
    $pk_col = $this->primary_key_col;

		return $this->$pk_col;
  }
  
  // Returns whether the object is dirty
	public function is_dirty()
	{
		foreach ($this->columns as $column)
		{
			if ($this->$column != $this->original_data[$column])
				return true;
		}

		return false;
	}

	// Clears all data
	public function clear()
	{
		$this->original_data = array();

		foreach ($this->columns as $column)
		{
			$this->$column = null;
			$this->original_data[$column] = null;
		}
	}

	// Resets the data to that of the original loaded values
	public function reset()
	{
		foreach ($this->columns as $column)
		{
			$this->$column = $this->original_data[$column];
		}
	}

	// Resets the original data to the current data
	private function clean()
	{
		foreach ($this->columns as $column)
		{
			$this->original_data[$column] = $this->$column;
		}
	}
  
  // Save/load tracking
  
  public function saved()
  {
    return $this->_saved;
  }
  
  public function reset_saved()
  {
    $this->_saved = false;
  }
  
  public function loaded()
  {
    return $this->_loaded;
  }
  
  public function reset_loaded()
  {
    $this->_loaded = false;
  }
  
  // Metadata
  public function __get($name)
  {
  
  
  }
}