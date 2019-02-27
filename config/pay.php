<?php

return [
	'alipay' => [
		'app_id'         => '2016092800613979',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1PbmTRmKLZt6XI/Ajjr4KIKwBo++pewbbQEpQX710DsQfG2sfIMKMSLBkDdx4dHSyXiUndhPqW7q8kj7k7V1N+8Y84l8qPRwahzEiXZ64JSCMnL8B/ObamVyj5qNm3LmcPgQHXjoPHOLT6MgrFsh5LECc+zNRazf1+43G+/AFnkJIQ0f/zrZ4rSRMuwPbtQrkjqdoNzHhN/RZZ8E7cq2Rd2ou8CxrHGkIphWWeI2eoQVI3ft49XmX8c2i2c2+a4iaWPKoGBafQ3fCzICAAa3WVIwu0+k9cHIWRbgTG0hdWiEv0Bxah+gpDCjMxLwQtba+6w5fs18CSZG7sbW0QFsHwIDAQAB',


        'private_key'    => 'MIIEpgIBAAKCAQEArKBX5wT968XFs5PTN/CMC9F8IE1TXhS3HtS849NuuUbZaR68V/BMm7xyJiokygPu7i9eflLaOfiI+2eYEXG9HnhyOy16MXyTa3uJMZFrwpl2DTcoQ6tuSjUd05WEw4WuqXce3NWUBsyyIQATrGV/gnzV/vMeaBpOmxtiYJuahy68yzZgD7YjyNYR+s19is6YAZGoORM77QF2pFgIw4/16vFRMZ5zKRm6ttMruvEm4E9xKgZOsuC17o0TZqBETH2zFMLTbVGVQ5mmMQBe4qoNoC+ojnDLHyQ7qq2aNKCpSy1avyYJe0PLWlKnVnj4RIo3HQQ0nhwx3VQXboDNxhdafwIDAQABAoIBAQCkHEt09OnxGzO4ZPCIXcbF9YFrtBdteYQda2GntXmt6g0GQpILiAdMhvp+DdKrutGK3r2Wmm2cbwrK9tE6xEZmkVsHp1Rjd2su42KqHPW1Ku9v4OAGdROEuLqCS8LLnmtN8FEG4IS5ciBl/wy0+EkgZE2M8tQVY+0WyBHehg+CMvywR4dWmPF+07PxSi34PMf6GQxy+KEuJOXZnQdGz/qXcq2K6qEIuxaG5ePSd9zLtCzxasMICV0ASw12r4X/aRsHbqFpx8h8+2HCpqO5ptEI6SxSLEYZsxgYs96T3HNO2j8lXTvLuW15erIL6ZDHx4fJh14B4oLraIw7PsUl934BAoGBANfeEChs4wHNqQhBGSn4rH8BUWUnU7VQwP2kRBxzgMalz62YYaedIohXTzBWTK5AAEGmeCc084nX8RgNF3peKYFmf+VHBBEohTL0MZwu4+A2Mav8ZthpLgqL0WclLFkIb2hmqeu6nY4Bqxe6BEp+ukl9G08FvMlLNNW/oZ16x4+5AoGBAMy4RncmkiVu/gVdrE8XJrLgCHxY3DzziE/7uh2ag+/dNEoRUJmidyJS6GRZWkBWFRmIdEyf7hJowQD/FeKWssEP6iRaM4nfG2yKN6iqyjWiBzwYtZEf901XWWiK8EYXCJJ+wvtETzmWSYCYRCy3HhjxwyFARqtwajyrzW/IGqf3AoGBAMk/Az1M1g2WWCGT3wvcA6Sb6/Ary8RbtrBAdic4HzQS3Rxv1YrX2ZvWGcNskSxaCmMp+RZELU+y+9d6TQsNwhAbKNztagM9DYDFW4LJzDP5EjHOq2rSE9RNKTnJ1CBk1sgz/fIAFmaPc5w5FUsF/lIIym7PYIMhE+66KseP8YAxAoGBAJ8PbnCVX2gGXQf+SCsLad4Bg5ZZ1ITnEzpR88GclHaCAGasHVvBh577m+ScKCtktvAnvXTrriZPjeYgS/jJ7gljPCESlIjC+KPUoHpEeOhDBCwFZs1jcNp+y9EWYqmSq+5m30grQwMTafioVhAIFzJ6ZYooTsBVj7WkJvJ7H3rxAoGBAITugR6ipfu7vWjEMuzLwHiCkeHrYK0jViKXrVv6nQaYk/z6XZa2SHoI7lDSh0f22DtaNaFK+vvaunRUhY/5RwgJcGJ3eyo7MCrj96KOmN56p9gD8WB6DU8T2U3q5YJCEeqVObsG1keN20MdSkO0S39h8Tv95eTAZbJWpaigA7cm',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
	],

	'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];