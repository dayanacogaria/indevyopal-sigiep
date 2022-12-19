<?php
class Tree
{
	private $_dbh;
	private $_elements = array();

	public function __construct()
	{
		try{

			//$this->_dbh = new PDO("mysql:host=localhost;dbname=sigep", "root", "");
                        $this->_dbh = new PDO("mysql:host=mysql.hostinger.co; dbname=u858942576_sigep", "u858942576_aaa", "cG0laRuRWV");
			$this->_dbh->exec("SET CHARACTER SET utf8");
	        $this->_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	        $this->_dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	    } 
	    catch (PDOException $e) 
	    {
            print "Error!: " . $e->getMessage();
            die();
        }
	}

	public function get()
	{
		$query = $this->_dbh->prepare("SELECT fp.id_unico, CONCAT(ef.nombre,' - ',f.nombre) as nombre1, 
                                                fp.flujo_si, CONCAT(efs.nombre,' - ',fs.nombre) as nombreS, fp.flujo_no, efn.nombre, fn.nombre
                                                FROM gg_flujo_procesal fp 
                                                LEFT JOIN gg_fase f ON fp.fase=f.id_unico 
                                                LEFT JOIN gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico 
                                                LEFT JOIN gg_flujo_procesal fps ON fps.id_unico = fp.flujo_si 
                                                LEFT JOIN gg_fase fs ON fps.fase=fs.id_unico
                                                LEFT JOIN gg_elemento_flujo efs ON fs.elemento_flujo = efs.id_unico 
                                                LEFT JOIN gg_flujo_procesal fpn ON fpn.id_unico = fp.flujo_no 
                                                LEFT JOIN gg_fase fn ON fpn.fase=fn.id_unico 
                                                LEFT JOIN gg_elemento_flujo efn ON fn.elemento_flujo = efn.id_unico");
		$query->execute();
		$this->_elements["masters"] = $this->_elements["childrens"] = array();
                if($query->rowCount() > 0)
		{
			foreach($query->fetchAll() as $element)
			{ 
                            $select = $this->_dbh->prepare('SELECT * FROM gg_flujo_procesal WHERE flujo_si ='.$element["id_unico"].'');
                            $select->execute();
                            	if($select->rowCount() ==0)
				{
                                    array_push($this->_elements["masters"], $element);
				}
				else
				{
					array_push($this->_elements["childrens"], $element);
				}
			}
		}
		return $this->_elements;
	}

	public static function nested($rows = array(), $parent_id = 0)
	{
		$html = "";
		if(!empty($rows))
		{
			$html.="<ul>";
                        
			foreach($rows as $row)
			{   
                            if($row["id_unico"] == $parent_id)
				
				{ 
					$html.="<li style='margin:5px 0px'>";
					$html.="<span><i class='glyphicon glyphicon-folder-open'></i></span>";
					$html.="<a href='#' data-status='{$row["id_unico"]}' style='margin: 5px 6px' class='btn btn-primary sombra btn-folder'>";
					if(count($row["flujo_si"]) > 1)
					{
						$html.="<span class='glyphicon glyphicon-minus-sign'></span>".$row['nombreS']."</a>";
					}
					else
					{
						$html.="<span class='glyphicon glyphicon-plus-sign'></span>".$row['nombreS']."</a>";
					}
					$html.=self::nested($rows, $row["flujo_si"]);
					$html.="</li>";
				}
			}
			$html.="</ul>";
		}
		return $html;
	}
}