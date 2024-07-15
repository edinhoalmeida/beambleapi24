<?php
namespace VideoRobot\Aws;

use Simplon\Mysql\Mysql;
use Simplon\Mysql\PDOConnector;

/**

https://github.com/fightbulc/simplon_mysql/tree/master


*/

class AwsDbService {

	private $pdoConnector;
	private $dbh;

	public function __construct(){
		$this->pdoConnector = new PDOConnector(
			_env('DB_HOST'),
			_env('DB_USERNAME'),
			_env('DB_PASSWORD'),
			_env('DB_DATABASE'));
		$this->dbh = new Mysql($this->pdoConnector->connect());
	}

	public function add_file($file){
		$file = explode("/",$file);
		$file = end($file);
		$row = $this->dbh->fetchRow('SELECT id,status FROM wv_videos_job_aws WHERE file = :file',
			['file' => $file]
		);
		if(is_null($row)){
			$now = date("Y-m-d h:i:s");
			$data = [
			    'videofeed_id'   => 0,
			    'file' => $file,
			    'status'  => 'new',
			    'created_at'  => $now
			];
			$jid = $this->dbh->insert('wv_videos_job_aws', $data);
			$data = [
			    'video_job_id' => $jid,
			    'status'  => 'new',
			    'created_at'  => $now
			];
			$this->dbh->insert('wv_videos_job_aws_status', $data);
			return $jid;
		}
		if($row['status']=='new'){
			return $row['id'];
		}
		return false;
	}

    public function new_status($jid, $status, $service_object){
        $data = [
            'video_job_id' => $jid,
            'status'  => $status,
            'service_object' => $service_object
        ];
        $this->dbh->insert('wv_videos_job_aws_status', $data);
    }

	public function has_video_new($status){
		$rows = $this->dbh->fetchRowMany('SELECT * FROM wv_videos_job_aws WHERE status = :status ORDER BY created_at ASC',
			['status' => $status]
		);
		return !empty($rows) ?  $rows : [];
	}


	public function update_attempts($jid, $attempts){
		$conds = [
    		'id' => $jid,
		];
		$data = [
    		'attempts' => $attempts
		];
		$result = $this->dbh->update('wv_videos_job_aws', $conds, $data);
	}

	public function update($jid, $status){
		$conds = [
    		'id' => $jid,
		];
		$data = [
    		'status'  => $status,
		];
		$result = $this->dbh->update('wv_videos_job_aws', $conds, $data);
	}

    public function update_token($jid, $token){
        $conds = [
            'id' => $jid,
        ];
        $data = [
            'token'  => $token,
        ];
        $result = $this->dbh->update('wv_videos_job_aws', $conds, $data);
    }

	public function update_urls($jid, $data){
		$conds = [
    		'id' => $jid,
		];
		$result = $this->dbh->update('wv_videos_job_aws', $conds, $data);
		$data = [
		    'video_job_id' => $jid,
		    'status'  => 'url_generated',
		    'created_at'  => date("Y-m-d h:i:s")
		];
		$this->dbh->insert('wv_videos_job_aws_status', $data);
	}

	public function update_one_url($jid, $column, $value){
		$conds = [
    		'id' => $jid,
		];
		$result = $this->dbh->update('wv_videos_job_aws', $conds, [$column=>$value]);
		$data = [
		    'video_job_id' => $jid,
		    'status'  => 'url_done ' . $column,
		    'created_at'  => date("Y-m-d h:i:s")
		];
		$this->dbh->insert('wv_videos_job_aws_status', $data);
	}

	public function update_status($jid, $status, $service_object, $video_status = null){
		$conds = [
    		'id' => $jid,
		];
		if(!empty($video_status)){
			$result = $this->dbh->update('wv_videos_job_aws', $conds, ['status'=>$video_status]);
		}
		$data = [
		    'video_job_id' => $jid,
		    'status'  => $status,
		    'service_object' => $service_object,
		    'created_at'  => date("Y-m-d h:i:s")
		];
		$this->dbh->insert('wv_videos_job_aws_status', $data);
	}

	public function fix_url_thumbs(){
		$rows = $this->dbh->fetchRowMany('SELECT id, url_video, url_thumb, attempts FROM wv_videos_job_aws WHERE url_video LIKE :urllike',
			['urllike' => "%w=719&h=1079%"]
		);
		if(empty($rows)){
			echo "corrigido " . 0 . " linhas\n";
			die();
		}
		foreach ($rows as $row) {
			$conds = [
    			'id' => $row['id'],
			];
			$subs = [
				'url_video'=>str_replace('w=719&h=1079','w=1080&h=1920', $row['url_video']),
				'url_thumb'=>str_replace('w=719&h=1079','w=1080&h=1920', $row['url_thumb'])
			];
			$result = $this->dbh->update('wv_videos_job_aws', $conds, $subs);
		}
		echo "corrigido " . count($rows) . " linhas\n";
		die();

	}

	public function erase_all(){
		$this->dbh->executeSql('TRUNCATE wv_videos_job_aws');
		$this->dbh->executeSql('TRUNCATE wv_videos_job_aws_status');
	}

}
