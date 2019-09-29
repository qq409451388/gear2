<?php
class EzPager{
	private $sql;
	private $db;
	private $limit;

	public function __construct($pageNum, $pageSize){
		if($pageNum < 1){
			$pageNum = 1;
		}
		$page = ($pageNum - 1) * $pageSize;
		$this->pageNum = $pageNum;
		$this->pageSize = $pageSize;
		$this->offset = $page;
		$this->limit = 0 == $pageSize ? '' : ' limit '.$page.','.$pageSize;
	}

	public function setDb(DAL $db){
		$this->db = $db;
        return $this;
	}

	private function getData(){
		$data = $this->db->query($this->sql.$this->limit);
		return empty($data) ? [] : $data;
	}

	private function getPageInfo(){
		$pageInfo = [];

		$midStr = DataTransfer::cutMiddleStr('select', 'from', $this->sql);
		$cntSql = str_replace($midStr, ' count(1) as total ',$this->sql);
		$pageInfo = $this->db->query($cntSql);
		$total = empty($pageInfo) ? 0 : current($pageInfo)['total'];

		return ['pageId' => (int)$this->pageNum,'pageSize' => (int)$this->pageSize, 'totalCnt' => (int)$total];
	}

	public function query($sql, $binds){
        $this->sql = $this->db->bindSqlParam($sql, $binds);
		return ['data' => $this->getData(), 'pageInfo' => $this->getPageInfo()];
	}

    public static function getXPageNav($url, $pageId, $pageSize, $total, Array $params = [], $btnNum = 10){
        $url = DOMAIN_URL.$url;
        $pageNum = ceil($total/$pageSize);
        $info = '';
        $paramsStr = empty($params) ? '' : '&'.http_build_query($params);

        if($pageId > 1){
            $urlPrev = $url.'?pageId='.($pageId-1);
            $urlPrev .= $paramsStr;
            $info .= '<a class="prev" href="'.$urlPrev.'">&lt;&lt;</a>';
        }

        $start = (int)($pageId/$btnNum) * $btnNum +1;
        $end = ceil($pageId/$btnNum) * $btnNum;
        if($end > $pageNum){
            $end = $pageNum;    
        }

        for($i=$start;$i<=$end;$i++){
            $tmpUrl = $url.'?pageId='.$i;
            $tmpUrl .= $paramsStr;
            if($i == $pageId){
                $info .= '<span class="current">'.$i.'</span>';
            }
            else
            {
                $info .= '<a class="num" href="'.$tmpUrl.'">'.$i.'</a>';
            }
        }
        if($pageId < $pageNum){
            $urlNext = $url.'?pageId='.($pageId+1);    
            $urlNext .= $paramsStr;
            $info .= '<a class="next" href="'.$urlNext.'">&gt;&gt;</a>';
        }
        
        return $info;
    }

	public static function getPageNav($pageId, $pageSize, $total, $styleType = 'style1'){
		$pathInfo = empty($_SERVER['PATH_INFO']) ? '' : $_SERVER['PATH_INFO'];
		$url = 'http://'.$_SERVER['SERVER_NAME'].$pathInfo;
		$str = '';
		$totalPage = (int)ceil($total/$pageSize);
		$boxClass = 'pageNav_boxClass';

		$str .= self::getStyle($styleType);

		$str .= "<div class='pageNav_box'>";
		if($pageId > 1){
			$prevUrl = $url.'?pageId='.($pageId-1);
			$str .= "<div class='".$boxClass."'><a href='".$prevUrl."'><<<</a></div>";
		}

		for($i=1;$i<=$totalPage;$i++){
			$currentClass = ($pageId == $i) ? 'pageNav_current' : '';

			$tmpUrl = $url.'?pageId='.$i;

			$str .= "<div class='".$boxClass.' '.$currentClass."'><a href='".$tmpUrl."'>".$i."</a></div>";
		}

		if($pageId != $totalPage){
			$nextUrl = $url.'?pageId='.($pageId+1);
			$str .= "<div class='".$boxClass."'><a href='".$nextUrl."'>>>></a></div>";
		}
		$str .= "<div style='clear:both'><div>";
		$str .= "</div>";
		return $str;
	}

	private static function getStyle($styleType = 'style2'){
		$style1 = '<style>
		.pageNav_boxClass {border:1px solid lightblue;display:inline-block;width:40px;height:40px;text-align:center;margin:0px 2px;}
		.pageNav_boxClass:hover {background-color:lightblue;}
		.pageNav_boxClass a {text-decoration:none;line-height:40px;width:40px;height:40px;display:inline-block;}
		.pageNav_current {background-color:lightblue;}
		</style>';

		$style2 = '<style>
		.pageNav_boxClass {border:1px solid lightgreen;display:inline-block;width:40px;height:40px;text-align:center;margin:0px 2px;}
		.pageNav_boxClass:hover {background-color:lightgreen;}
		.pageNav_boxClass a {text-decoration:none;line-height:40px;width:40px;height:40px;display:inline-block;}
		.pageNav_current {background-color:lightgreen;}
		</style>';

		$style3 = '<style>
		.pageNav_boxClass {border:1px solid lightgreen;display:inline-block;width:40px;height:40px;text-align:center;margin:0px 2px;border-radius:5px}
		.pageNav_boxClass:hover {background-color:lightgreen;}
		.pageNav_boxClass a {text-decoration:none;line-height:40px;width:40px;height:40px;display:inline-block;}
		.pageNav_current {background-color:lightgreen;}
		</style>';

		return $$styleType;
	}
}
