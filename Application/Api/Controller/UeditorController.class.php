<?php
/**
 * ueditor 百度编辑器文件上传控制器
 * @author lan
 */
namespace Api\Controller;
use Think\Controller;

/**
 * ueditor 百度编辑器文件上传控制器
 * 必须有管理后台编辑内容的权限
 * @todo 上传视频、涂鸦
 */
class UeditorController extends Controller {
	private $CONFIG;
	private $UPLOAD_CONFIG = array(
		'rootPath'   =>    './Upload/',
		'autoSub'    =>    true,
		'subName'    =>    array('date','Ymd'),
	);

	public function index(){
		// 获取配置
		$config_file = file_get_contents(APP_PATH . "Api/Conf/ueditor.json");
		$this->CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $config_file), true);

		$action = $_GET['action'];

		switch ($action) {
		    case 'config':
		        $result =  $this->CONFIG;
		        break;

		    /* 上传图片 */
		    case 'uploadimage':

		    /* 上传涂鸦 */
		    // todo: post过来的是base64数据
		    case 'uploadscrawl':

		    /* 上传视频 */
		    case 'uploadvideo':

		    /* 上传文件 */
		    case 'uploadfile':
		        $result = $this->actionUpload($action);
		        // parse result
		        $result = $this->parseData($result);
		        break;

		    /* 列出图片 */
		    case 'listimage':
		        
		    /* 列出文件 */
		    case 'listfile':
		        $result = $this->actionList($action);
		        break;

		    /* 抓取远程文件 */
		    case 'catchimage':
		        $result = $this->actionCrawler();
		        break;

		    default:
		        $result = array(
		            'state'=> '请求地址出错'
		        );
		        break;
		}

		$this->output($result);

	}

	/**
	 * 调用上传方法
	 */
	private function actionUpload($action = ''){
		if($action == 'uploadimage' || $action == 'uploadfile' || $action == 'uploadvideo'){
			// 上传图片、文件及视频
			$config = $this->UPLOAD_CONFIG;
			$upload = new \Think\Upload($config);
			$info = $upload->upload();
			if(!$info){
				// 上传失败
				return array('code'=>102, 'message'=>$upload->getError());
			}else{
				// 上传成功
				$info = $info['upfile'];
				return array(
					'code'=>0, 
					'message'=>'SUCCESS', 
					'data'=>array(
						'filePath'=>$config['rootPath'] . $info['savepath'] . $info['savename'],
						'filename'=>$info['name'],
					),
				);
			}
		}elseif ($action == 'uploadscrawl') {
			# todo base64上传涂鸦
			return array('code'=>103, 'message'=>"暂不支持涂鸦");
		}
	}

	/**
	 * 列出图片和文件
	 */
	private function actionList($action = ''){

		switch ($action) {
		    /* 列出文件 */
		    case 'listfile':
		    	$allowFiles = 'pdf|doc|xls|docx|xlsx';
		        $listSize = 10;
		        $path = "/Upload/";
		        break;
		    /* 列出图片 */
		    case 'listimage':
		    default:
		        $allowFiles = "png|jpg|jpeg|gif";
		        $listSize = 20;
		        $path = "/Upload/";
		}

		/* 获取参数 */
		$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
		$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
		$end = $start + $size;

		/* 获取文件列表 */
		$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
		$files = $this->getfiles($path, $allowFiles);
		if (!count($files)) {
		    return array(
		        "state" => "no match file",
		        "list" => array(),
		        "start" => $start,
		        "total" => count($files)
		    );
		}

		/* 获取指定范围的列表 */
		$len = count($files);
		for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
		    $list[] = $files[$i];
		}

		/* 返回数据 */
		$result = array(
		    "state" => "SUCCESS",
		    "list" => $list,
		    "start" => $start,
		    "total" => count($files)
		);

		return $result;
	}

	/**
	 * 抓取远程文件
	 * 暂时屏蔽此功能
	 */
	private function actionCrawler(){
		// 屏蔽此功能
		return array(
		    'state'=> 'ERROR',
		    'list'=> array()
		);
	}

	/**
	 * 将ApiHelper格式的数据转换成编辑器输出数据格式
	 * @param array $data ApiHelper输出格式
	 * @return array 数组结构
	 * array(
	 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
	 *     "url" => "",            //返回的地址
	 *     "title" => "",          //新文件名
	 *     "original" => "",       //原始文件名
	 *     "type" => ""            //文件类型
	 *     "size" => "",           //文件大小
	 * )
	 */
	private function parseData($data = array()){
		if($data['code'] == 0 && $data['message'] == 'SUCCESS'){
			// array('attachmentNo'=>'', 'filePath'=>'')
			$data = $data['data'];
			return array(
				"state"	=>	"SUCCESS",
				"url"	=>	$data['filePath'],
				"title"	=>	$data['filename'],
				"original"=>$data['filename'],
				"type"	=>	"",
				"size"	=>	0,
			);
		}else{
			return array(
				"state"	=>	$data['message'] ? $data['message'] : "错误码：".$data['code'],
			);
		}
	}

	/**
	 * 输出数据内容
	 */
	private function output($result = array()){
		$result = json_encode($result);

		/* 输出结果 */
		if (isset($_GET["callback"])) {
		    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
		        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
		    } else {
		        echo json_encode(array(
		            'state'=> 'callback参数不合法'
		        ));
		    }
		} else {
		    echo ($result);
		}

		exit();
	}

	/**
	 * 遍历获取目录下的指定类型的文件
	 * @param $path
	 * @param array $files
	 * @return array
	 */
	private function getfiles($path, $allowFiles, &$files = array())
	{
	    if (!is_dir($path)) return null;
	    if(substr($path, strlen($path) - 1) != '/') $path .= '/';
	    $handle = opendir($path);
	    while (false !== ($file = readdir($handle))) {
	        if ($file != '.' && $file != '..') {
	            $path2 = $path . $file;
	            if (is_dir($path2)) {
	                $this->getfiles($path2, $allowFiles, $files);
	            } else {
	                if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
	                    $files[] = array(
	                        'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
	                        'mtime'=> filemtime($path2)
	                    );
	                }
	            }
	        }
	    }
	    return $files;
	}

}