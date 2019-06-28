<?php return [
	
	// 数据库配置
	"database"=>[
		// 微惠云数据库配置
        "yun"=>[
            "drive"=>"mysql",
            "host"=>"127.0.0.1",
            "port"=>"3306",
            "username"=>"yun",
            "password"=>"EyLYLe7yxtnZcXMJ",
            "database"=>"yun",
            "charset"=>"utf8"
        ],
        // 微擎数据库配置
        "w7"=>[
            "drive"=>"mysql",
            "host"=>"127.0.0.1",
            "port"=>"3306",
            "username"=>"w7",
            "password"=>"aeRHX8wFa8Z53Cix",
            "database"=>"w7",
            "charset"=>"utf8"
        ],
	],
	// 应用配置
	"app" => [
	    'default_database' => 'yun',
        'key' => 'f1a5sd1f032asd1f6as15df1as35d1f5a',
	],
	// 框架配置
	"frame" => [
		// 是否开发
		"debug" => false,
		// 响应配置
		"response" => [
			// 响应格式
			"format"   => "json",
			// 响应模板
			"template" => [
				"success" => function($arge) {
					$message = isset($arge[0])?$arge[0]:"";
					$data    = isset($arge[1])?$arge[1]:null;
					return ['code'=>0, 'message'=>$message, 'data'=>$data];
				},
				"error" => function($arge) {
                    $code    = isset($arge[0])?$arge[0]:40001;
					$message = isset($arge[1])?$arge[1]:"";
					$data    = isset($arge[2])?$arge[2]:null;
					return ['code'=>$code, 'message'=>$message, 'data'=>$data];
				}
			],
		]
	],
];