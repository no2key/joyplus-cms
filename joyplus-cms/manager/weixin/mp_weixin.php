<?php
/**
  * wechat php test
  */
require_once (dirname(__FILE__)."/inc/conn.php");
$topicKeywordValues =require(dirname(__FILE__).'/weixin_config.php');
//define your token
define("TOKEN", "D2803E3062DF75E874A44F12ECDC75E6");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();


class wechatCallbackapiTest
{
	public function valid()
    {
    	
    	 $this->responseMsg();
    	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
//        $postStr='22';
    	if (!empty($postStr)){
    		try{
    		  $this->responseMsg();
    		}catch(Exception $e){
    			//e.p
    		}
    		exit();
    	}
    	
       // $echoStr = $_GET["echostr"];

        //valid signature , option
       // if($this->checkSignature()){
        //	echo $echoStr;
        //	exit;
       // }
       
    }
    
    public function handleMenu($reqEventKey, $fromUsername, $toUsername, $time){
    	writetofilelog("response-log.log",$reqEventKey);
    	$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
//    	$linkTpl='<xml>
//					<ToUserName><![CDATA[%s]]></ToUserName>
//					<FromUserName><![CDATA[%s]]></FromUserName>
//					<CreateTime>%s</CreateTime>
//					<MsgType><![CDATA[%s]]></MsgType>
//					<Title><![CDATA[%s]]></Title>
//					<Description><![CDATA[]]></Description>
//					<Url><![CDATA[%s]]></Url>
//					<MsgId>1234567890123456</MsgId>
//					</xml> ';
//    	if($reqEventKey ==='V1001_MORE_LIKE_VIDEO'){
//    		$msgType = "text";
//            $contentStr = "Hey, 发现了好的视频,可以告诉我们,我们可以给大家分享哦 ";
//            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr);
//            echo $resultStr;
//    	}else
    	 if($reqEventKey ==='V1001_MORE_VOICE'){
    		$msgType = "text";
            $contentStr = "Well, 亲有什么建议,给我们提哦. 或者, 发现了好的视频,也可以告诉我们,我们可以给大家分享哦 ";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr);
            echo $resultStr;
    	}else if($reqEventKey ==='V1001_MORE_APP'){
    		$msgType = "text";
            $contentStr = "获取官方应用\r\niOS下载: \r\nhttp://ums.bz/GGISox/ \r\nAndroid下载: \r\nhttp://um0.cn/14FOW7/";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr);
             echo $resultStr;
    	}else if(!is_null($reqEventKey) && strpos($reqEventKey, 'V1001_MOVIE_') !==false){
    		$topicid= str_replace("V1001_MOVIE_", '', $reqEventKey);
    		writetofilelog("response-log.log",$topicid);
    		$movies= $this->searchMovieByTopic($topicid);					
			if(is_array($movies)&& count($movies)>0){
				$textImgTpl=$this->genereatePicText($movies);  
                  $msgType = "news";
                    $resultStr = sprintf($textImgTpl, $fromUsername, $toUsername, $time, $msgType);
                    writetofilelog("response-log.log",$resultStr);
                        echo $resultStr;
                       
			}
    	}else if($reqEventKey ==='V1001_MORE_SHOW'){
    		$movies= $this->searchMovieByTopic('152');//综艺悦榜					
			if(is_array($movies)&& count($movies)>0){
				$textImgTpl=$this->genereatePicText($movies);  
                  $msgType = "news";
                    $resultStr = sprintf($textImgTpl, $fromUsername, $toUsername, $time, $msgType);
                        echo $resultStr;
			}
    	}
    	
    	
    }
    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		 
		writetofilelog("request-log.log", $postStr);
//        $postStr="2";
      	//extract post data
		if (!empty($postStr)){
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keywords = trim($postObj->Content);
                $time = time();
                $reqMsgType =trim($postObj->MsgType);
                writetofilelog("response-log.log", $reqMsgType);
                if($reqMsgType === 'event'){                	
                	$reqEvent = trim($postObj->Event);
                	writetofilelog("response-log.log",$reqEvent);
                	if($reqEvent ==='subscribe') {
                		$msgType = "text";
                	      $contentStr = "我是小悦，好开心你终于关注我啦！如果某一部好看的电视剧让你一直牵肠挂肚，如果某一场精彩电影让你一直心驰神往，如果你对某一个影片片段有一种无法割舍的情怀，或者如果某一句唯美的台词让你感触深刻... ... 按以下规则回复，让好影片与你不期而遇！ \r\n回复“A导演名”查看该导演的影片信\r\n回复“B演员名”查看该演员所主演的影片信息 \r\n回复“C影片名”查看相关的影片信息\r\n赶紧试一试哦，让好电影来找你！\r\n 悦视频下载地址: http://app.joyplus.tv/yueshipin.php";
                    
                	      $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr);
                	      echo $resultStr;
                	}else if($reqEvent ==='CLICK') {
                		$reqEventKey = trim($postObj->EventKey);
                		$this->handleMenu($reqEventKey, $fromUsername, $toUsername, $time);
                	}
                } else if ($reqMsgType === 'text'){            
				  if(!empty( $keywords ) && strlen($keywords)>0){ 
					writetofilelog("response-log.log", $keywords);
					if('Hello2BizUser' === $keywords){
						 $msgType = "text";
                	      $contentStr = " 我是小悦，好开心你终于关注我啦！如果某一部好看的电视剧让你一直牵肠挂肚，如果某一场精彩电影让你一直心驰神往，如果你对某一个影片片段有一种无法割舍的情怀，或者如果某一句唯美的台词让你感触深刻... ... 按以下规则回复，让好影片与你不期而遇！ \r\n回复“A导演名”查看该导演的影片信\r\n回复“B演员名”查看该演员所主演的影片信息 \r\n回复“C影片名”查看相关的影片信息\r\n赶紧试一试哦，让好电影来找你！ ";
                    
                	      $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr);
                	      echo $resultStr;
					}else {
				        $keywordType=substring($keywords, 1 , 0); 
						writetofilelog("response-log.log", $keywordType);
						$keyword=substring($keywords, strlen($keywords)-1,1);
						$keyword=trim($keyword);
						writetofilelog("response-log.log", $keyword);
						//var_dump($keyword);
						if(!($keywordType ==='A' || $keywordType ==='a' || $keywordType ==='B' || $keywordType ==='b'  || $keywordType ==='C' || $keywordType ==='c'  )){
//							  $msgType = "text";
//	                	      $contentStr = " 你的反馈已经收到了，晚点联系你哦。 ";	                    
//	                	      $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr);
//	                	      echo $resultStr;
						}else {							
							writetofilelog("response-log.log", $keyword);
							$movies= $this->searchMovie($keyword, $keywordType);					
							if(is_array($movies)&& count($movies)>0){
								$textImgTpl=$this->genereatePicText($movies);  
		              		    $msgType = "news";
		                	    $resultStr = sprintf($textImgTpl, $fromUsername, $toUsername, $time, $msgType);
		                	    
		                        writetofilelog("response-log.log", $resultStr);
		                        echo $resultStr;
							}else {
								 $msgType = "text";
		                          $contentStr = "你的回复小悦收到喽，小悦费了很大的劲但是没有找到相关影片，能不能请北鼻你再按以下规则回复其它信息：  \r\n回复“ 电影/电视剧/动漫/综艺名称”查看相关的影片信息\r\n回复“A导演”查看该导演的影片信\r\n回复“B演员”查看该演员所主演的影片信息 \r\n回复“C影片名”查看相关的影片信息 \r\n让小悦更准确的给您推荐相关影片！   ";
		                    
			                	 $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr);
			                	echo $resultStr;
							}
						}
					}
					
                }else{
                	  
                	 $msgType = "text";
                	 $contentStr = "我是小悦，好开心你终于关注我啦！如果某一部好看的电视剧让你一直牵肠挂肚，如果某一场精彩电影让你一直心驰神往，如果你对某一个影片片段有一种无法割舍的情怀，或者如果某一句唯美的台词让你感触深刻... ... 按以下规则回复，让好影片与你不期而遇！ \r\n回复“A导演名”查看该导演的影片信\r\n回复“B演员名”查看该演员所主演的影片信息 \r\n回复“C影片名”查看相关的影片信息\r\n赶紧试一试哦，让好电影来找你！ ";
                    
                	 $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$contentStr);
                	echo $resultStr;
                }
             }
        }else {
        	echo "";
        	exit;
        }
    }
	
    private function getNotifyMsg($type){
    	
    }
    
    private function getTopicIDByName($keyword){
    	$keywordTopicID= array(
    	    '141'=>',动作片,动作电影,动作,武打,',
			'142'=>',伦理片,伦理电影,伦理,家庭伦理,',
			'143'=>',喜剧片,喜剧电影,哈哈,喜剧,搞笑,',
			'144'=>',动画,动漫,动画片,动漫电影,',
			'145'=>',悬疑片,悬疑电影,悬疑,侦探,',
			'146'=>',恐怖片,恐怖电影,惊悚片,惊悚电影,恐怖,惊悚,',
			'2704'=>',爱情片,爱情电影,爱情,爱,love,',
			'2705'=>',三级片,黄片,色情片,情色,色情,三级,A片,欲,一路向西,av,AV,',
			'148'=>',美剧,欧美剧,美国,',
			'147'=>',大陆剧,内地剧,',
			'2250'=>',TVB,港剧,香港,HK,',
			'150'=>',台剧,台湾,TW,',
			'918'=>',韩剧,韩国,韩国片,',
			'149'=>',日剧,日本,日本片,',
    	    '109'=>',女人,女生节,完美女人,妇女节,三八,三八节,38,',
            '30'=>',女同,蕾丝,女人间的微妙关系,Lesbian,LES,拉拉,拉子,蕾丝边,百合,',
			'28'=>',情侣,情人节,214,情人,lover,恋人,',
			'2682'=>',IMDB,imdb,',
			'2491'=>',重口味,荤',
			'123'=>',母爱,母亲节,母亲,妈妈,妈,mother,母,',
			'18'=>',小萝莉,小朋友,儿童节,六一,小正太,六一儿童节,亲子,儿童,小孩,',
    	);
    	global $topicKeywordValues;
    	$keywordTopicID=$topicKeywordValues;
    	
    	$keys= array_keys($keywordTopicID);
    	foreach ($keys as $key){
    		$value = $keywordTopicID[$key];
    		if(strpos($value, ",".$keyword.",") !==false){
    			return $key;
    		}
    	}
    	return false;
    }
    private function searchMovie($keyword,$keywordType){
    	global $db;
    	$flag=true;    	
    	$sql='SELECT d_id AS prod_id, d_name AS prod_name, d_pic AS prod_pic_url, d_content AS prod_sumary, d_pic_ipad FROM mac_vod, mac_vod_topic_items WHERE d_hide =0 AND vod_id = d_id AND topic_id =140 ORDER BY disp_order DESC , d_level DESC , d_play_num DESC , d_type ASC , d_good DESC , d_time DESC LIMIT 0, 3';
    	if(isset($keyword) && !is_null($keyword) && strlen(trim($keyword))>0){
	    	//$sql = 'select d_id as prod_id, d_name as prod_name, d_type as prod_type,d_pic as prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area where mac_vod where d_hide=0  and d_type in (1,2,3,131) ';
	    	$sql = 'select d_id as prod_id, d_name as prod_name, d_pic as prod_pic_url,d_content as prod_sumary ,d_pic_ipad from mac_vod where d_hide=0  and d_type in (1,2,3,131) ';
	    	if($keywordType==='c'){
	    		$topicid=$this->getTopicIDByName($keyword);
	    		writetofilelog("response-log.log", $topicid);
	    		if($topicid){
	    		    writetofilelog("response-log.log", $topicid);
	    			$sql='SELECT d_id AS prod_id, d_name AS prod_name, d_pic AS prod_pic_url, d_content AS prod_sumary, d_pic_ipad FROM mac_vod, mac_vod_topic_items WHERE d_hide =0 AND vod_id = d_id AND topic_id ='.$topicid.' ORDER BY disp_order DESC , d_level DESC , d_play_num DESC , d_type ASC , d_good DESC , d_time DESC LIMIT 0, 3';
    	            $flag=false;
    	        }else {
	    			$sql = $sql. ' and  (d_name like \'%'.$keyword.'%\' or d_enname like \'%'.$keyword.'%\' ) ';
	    		}
	    		
	    	}
	        if($keywordType ==='A' || $keywordType ==='a' ){
	    		$sql = $sql. ' and  d_directed like \'%'.$keyword.'%\'';
	    	}
	        if($keywordType ==='B' || $keywordType ==='b' ){
	    		$sql = $sql. ' and  d_starring like \'%'.$keyword.'%\'';
	    	}
	    	if($flag){
	    	  $sql=$sql. ' order by d_level desc ,d_play_num desc,d_type asc ,d_good desc,d_time DESC LIMIT 0, 3 ';
	    	}
    	}
    	$rs = $db->query($sql);
    	writetofilelog("response-log.log", $sql);
    	$movies=array();
    	while ($row = $db ->fetch_array($rs)){
    		$movie=array();
    		$movie['prod_id']=$row['prod_id'];
    		$movie['prod_name']=$row['prod_name'];
    		$movie['prod_pic_url']=$row['prod_pic_url'];
    		$movie['prod_sumary']=$row['prod_sumary'];    		
    		if(is_null($row['d_pic_ipad']) || $row['d_pic_ipad'] ===''){
    			$movie['prod_pic']=$row['prod_pic_url'];
    		}else {
    			$movie['prod_pic']=$this->parsePadPost($row['d_pic_ipad']);
    		}
	  		$movies[]=$movie;
	    }
	    unset($rs);
	   // var_dump($movies);
	    return $movies;
    }
    
     private function searchMovieByVodType($type){
     	$sql = 'select d_id as prod_id, d_name as prod_name, d_pic as prod_pic_url,d_content as prod_sumary ,d_pic_ipad from mac_vod where d_hide=0  and d_type ='.$type.'  order by d_time DESC,d_play_num desc,d_level desc ,d_good desc LIMIT 0, 4 ';
        return $this->searchMovieMenu($sql);
     }
     
     private function searchMovieByTopic($topicID){
     	$sql='SELECT d_id AS prod_id, d_name AS prod_name, d_pic AS prod_pic_url, d_content AS prod_sumary, d_pic_ipad FROM mac_vod, mac_vod_topic_items WHERE d_hide =0 AND vod_id = d_id and flag=1 AND topic_id ='.$topicID.' ORDER BY disp_order DESC , d_level DESC , d_play_num DESC , d_type ASC , d_good DESC , d_time DESC LIMIT 0, 4';
    	return $this->searchMovieMenu($sql);
     }
     
     private function searchMovieMenu($sql){
    	global $db;
    	$rs = $db->query($sql);
    	writetofilelog("response-log.log", $sql);
    	$movies=array();
    	while ($row = $db ->fetch_array($rs)){
    		$movie=array();
    		$movie['prod_id']=$row['prod_id'];
    		$movie['prod_name']=$row['prod_name'];
    		$movie['prod_pic_url']=$row['prod_pic_url'];
    		$movie['prod_sumary']=$row['prod_sumary'];    		
    		if(is_null($row['d_pic_ipad']) || $row['d_pic_ipad'] ===''){
    			$movie['prod_pic']=$row['prod_pic_url'];
    		}else {
    			$movie['prod_pic']=$this->parsePadPost($row['d_pic_ipad']);
    		}
	  		$movies[]=$movie;
	    }
	    unset($rs);
	   // var_dump($movies);
	    return $movies;
    }
    
	function parsePadPost($pic_url){
	  if(isset($pic_url) && !is_null($pic_url)){
	      $prodPicArray = explode("{Array}", $pic_url);	  
	      if(count($prodPicArray)>0){
		      return $prodPicArray[0];
		  }
	  }
	  return $pic_url;
	}
    private function genereatePicText($movies){
        $count= count($movies);
        $movie=$movies[0];
    	$msg='<xml> <ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><ArticleCount>'.$count.'</ArticleCount><Articles>';
	    if($count===1){   	
			 $msg=$msg.'<item>
			 <Title><![CDATA[《'.$movie[prod_name].'》]]></Title> 
			 <Description><![CDATA['.substring($movie[prod_sumary],100,0).']]></Description>
			 <PicUrl><![CDATA['.($movie[prod_pic]).']]></PicUrl>
			 <Url><![CDATA[http://weixin.joyplus.tv/info.php?prod_id='.($movie[prod_id]).']]></Url>
			 </item>
			 ';
	    }else {
	  	  for($i=0;$i<$count;$i++){
	  	 	 $movie=$movies[$i];
		  	  $msg=$msg.'<item>
			 <Title><![CDATA[《'.$movie[prod_name].'》  '.substring($movie[prod_sumary],30,0).']]></Title> 
			 <Description><![CDATA[]]></Description>
			 <PicUrl><![CDATA['.($movie[prod_pic]).']]></PicUrl>
			 <Url><![CDATA[http://weixin.joyplus.tv/info.php?prod_id='.($movie[prod_id]).']]></Url>
			 </item>
			 ';
	  	 }
  	  
       }
       $msg=$msg.'</Articles>
 <FuncFlag>1</FuncFlag>
 </xml> ';
      // var_dump($msg);
       return $msg;
    }
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

  function writetofilelog($file_name,$text) {
     $date_time = date("Y-m-d H:i:s");
     $text = "$date_time: ".$text;
	 $date = date("Y-m-d");
	 $fileArray = explode(".", $file_name);
	 if(count($fileArray)==2){
	 	$file_name =$fileArray[0].'_'.$date.'.'.$fileArray[1];
	 } 
	 $file_name = dirname(__FILE__).'/logs/'.$file_name;
	// var_dump($file_name);
	if (!file_exists($file_name)) {
      touch($file_name);
      chmod($file_name,"744");
    }

   $fd = @fopen($file_name, "a");
   @fwrite($fd, $text."\r\n");
   @fclose($fd);

}


?>