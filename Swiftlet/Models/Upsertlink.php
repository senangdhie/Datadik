<?php
namespace Swiftlet\Models;

class Upsertlink extends \Swiftlet\Model
{
	protected $db = null;
	private $_rawQuery = null,$_link = null,$_host =  null,$_base = null,$_user = null,$_pass = null, $_values = array(), $_err = null;
	
	public function __construct(\Swiftlet\Interfaces\App $app)
	{
		parent::__construct($app);
		
	}
	
	public function rawQuery($query,$link_mode = 0)
	{
		$this->_link = $link_mode;
		$this->_rawQuery = $query;
		$conf = $this->app->getConfig('dbo');
		$_cf = $conf[$link_mode];
		
		$constr = "pgsql:host=".$_cf[0].";port=5432;dbname=".$_cf[1].";user=".$_cf[2].";password=".$_cf[3];
		try {
			$this->db = new \PDO($constr);
			$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			var_dump($e);
			die("<html><head><title>Sistem Sibuk</title></head><body><h3>Sistem Sedang Sibuk</h3></body></html>");
		}
		return $this;
	}
	
	public function run($noResult = false)
	{
		if(is_null($this->db))
		{
			return false;
		}
		$query = $this->_rawQuery;
		try{
            $preparedQuery = $this->db->prepare($query);
            $preparedQuery->execute($this->_values);
        }catch(\Exception $e){
			$this->_err = $e->getMessage();
            return false;
			//throw new \Exception($e->getMessage());
			//echo "Internal Problem : Server gagal response";
        }
        
        if(!$noResult){
            $rows = array();
            while($row = $preparedQuery->fetch(\PDO::FETCH_ASSOC)){
                $rows[] = $row;
            }

            return $rows;
        }else{
            return $preparedQuery;
        }
	}
	
	public function fetch()
	{
        $rows = $this->run();
        return $rows ? $rows : false;
    }
	
	public function getQuery()
	{
		return $this->_rawQuery;
	}
	
	public function getError()
	{
		return $this->_err;
	}
}
?>