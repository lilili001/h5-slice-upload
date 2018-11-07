# 前端js上传文件切片

## 实现功能:

1. 前端多文件上传
2. 前端文件切片, 并命名uuid
3. 后端接受切片并根据文件名称保存至文件夹
4. 后端判断是否是最后一个切片,合并切片

## 技术要点

1. h5文件切片

切片上传请求参数:
```
filename: file-5bad6aab-cf7d-bfdb-356a-36d7b4ab1e1e.jpg
fragname: frag-1e0d1311-2369-317b-262e-04a9f427ea8c
file: (binary)
fragindex: 0
total: 2
```

2. es6 async await
3. php monolog composer包

## 安装指导

1. 下载文件到本地
> git clone https://github.com/lilili001/h5-slice-upload.git
2. 安装composer 包
> composer install 
3. 访问页面index.html

## 说明
文件合并的两种方法:

```
$orgFileName = $_POST['filename'];
```

方法一:

```
//合并file文件夹中的frag为最终文件
if( $_POST['fragindex'] == $_POST["total"] -1 ){
    $blob = "";
    $fragDir = FRAGPATH.$filename;
    $handler = @opendir($fragDir);
    //获取filename
    while ( ( $fragFileName = readdir($handler) ) !== false ){
        // 务必使用!==，防止目录下出现类似文件名“0”等情况
        if ($fragFileName !== "." && $fragFileName !== "..")
        {
            $blob .= file_get_contents( $fragDir . "/" . $fragFileName );
            //删除切片文件
            //@unlink($fragDir . "/" . $fragFileName);
        }
    }
    //合并切片到文件
    file_put_contents( FILEPATH. "/" . $filename . ".". $ext , $blob );
    //删除切片文件夹
    //@rmdir($fragDir);
```

方法二:
```
    while ( ( $fragFileName = readdir($handler) ) !== false ){
        $fp = fopen( FILEPATH . '/' . $orgFileName,"ab" );
        // 务必使用!==，防止目录下出现类似文件名“0”等情况
        if ($fragFileName !== "." && $fragFileName !== "..")
        {
            //$blob .= file_get_contents( $fragDir . "/" . $fragFileName );
            $value = $fragDir . "/" . $fragFileName;
            $handle = fopen($value,"rb");
            fwrite($fp,fread($handle,filesize($value)));
            fclose($handle);

            //删除切片文件
            @unlink($fragDir . "/" . $fragFileName);
        }
    }
```    