public function public_upload_apk()
    {
        $app = $this->_mdb['apps'] -> get_one(array('id'=>intval($_GET['appid'])));

        $sourceDir = SAMPLE_PKG_PATH."source/{$app['shortname']}";
        if (!is_dir($sourceDir))
        {
            mkdir($sourceDir, 0777, true);
        }

        //�����ͼ��
        $filename = $_GET['filename'];
        $fileext = fileext($filename);
        $alowexts = "apk";
        if(!preg_match("/^(".$alowexts.")$/", $fileext))
        {
            $output = json_encode(array('success'=>0, 'err_code'=>1, 'msg'=>'�ļ����ʹ���,�ϴ�ʧ��,�������ϴ�'));
            goto output;
        }

        $filename = "{$app['shortname']}_{$_GET['version']}.apk";
        $realname = "{$sourceDir}/{$filename}";
        if(file_exists($realname))
        {
            $output = json_encode(array('success'=>0, 'err_code'=>2, 'msg'=>'�ļ�����,�ϴ�ʧ��'));
            goto output;
        }

        $data = file_get_contents("php://input");

        $handle = fopen($realname, 'w+');
        $server_file_size = fwrite($handle, $data);
        fclose($handle);

        $client_size = $_GET['file_size'];
        if($server_file_size == $client_size)
        {
            $output = json_encode(array(
                    'success'=>1,
                    'title'=>$filename,
                    'filename'=>$filename
                )
            );
        }else{
            $output = json_encode(array('success'=>0, 'err_code'=>3, "src=".strlen($client_size)
            ." bytes={$server_file_size},�ϴ�������,�ϴ�ʧ��,,�������ϴ�"));
        }

        output:
            header("Content-Type: application/json"); //���Է�ֹUC������Զ����<audio>Ԫ��
            echo $output;

    }

