<?php

namespace App\Babel\Extension\noj;

class Languages
{
    public static function get()
    {
        $default_env = ["LANG=en_US.UTF-8", "LANGUAGE=en_US:en", "LC_ALL=en_US.UTF-8"];
        $default_factors = ['cpu' => ['factor' => 1, 'constant' => 0], 'memory' => ['factor' => 1, 'constant' => 0]];
        return [
            'c_lang_config' => [
                'compile' => [
                    'src_name' => 'main.c',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/gcc -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c99 {src_path} -lm -o {exe_path}',
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => 'c_cpp',
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => $default_factors
            ],
            'c_lang_config_spj' => [
                'compile' => [
                    'src_name' => 'spj-{spj_version}.c',
                    'exe_name' => 'spj-{spj_version}',
                    'max_cpu_time' => 3000 * 5,
                    'max_real_time' => 10000 * 5,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/gcc -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c99 {src_path} -lm -o {exe_path}'
                ],
                'run' => [
                    'exe_name' => 'spj-{spj_version}',
                    'command' => '{exe_path} {in_file_path} {out_file_path} {user_out_file_path}',
                    'seccomp_rule' => 'c_cpp',
                    'env' => $default_env
                ],
                'factors' => $default_factors
            ],
            'c11_lang_config' => [
                'compile' => [
                    'src_name' => 'main.c',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/gcc -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c11 {src_path} -lm -o {exe_path}',
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => 'general',
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => $default_factors
            ],
            'cpp_lang_config' => [
                'name' => 'cpp',
                'compile' => [
                    'src_name' => 'main.cpp',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/g++ -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c++11 {src_path} -lm -o {exe_path}',
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => 'c_cpp',
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => $default_factors
            ],
            'cpp_lang_config_spj' => [
                'name' => 'cpp',
                'compile' => [
                    'src_name' => 'spj-{spj_version}.cpp',
                    'exe_name' => 'spj-{spj_version}',
                    'max_cpu_time' => 3000 * 5,
                    'max_real_time' => 10000 * 5,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/g++ -DONLINE_JUDGE -O2 -I /opt/testlib/ -w -fmax-errors=3 -std=c++11 {src_path} -lm -o {exe_path}'
                ],
                'run' => [
                    'exe_name' => 'spj-{spj_version}',
                    'command' => '{exe_path} {in_file_path} {out_file_path} {user_out_file_path}',
                    'seccomp_rule' => 'general',
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => $default_factors
            ],
            'cpp14_lang_config' => [
                'name' => 'cpp14',
                'compile' => [
                    'src_name' => 'main.cpp',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/g++ -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c++14 {src_path} -lm -o {exe_path}',
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => 'c_cpp',
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => $default_factors
            ],
            'cpp17_lang_config' => [
                'name' => 'cpp17',
                'compile' => [
                    'src_name' => 'main.cpp',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/g++ -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c++17 {src_path} -lm -o {exe_path}',
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => 'c_cpp',
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => $default_factors
            ],
            'java_lang_config' => [
                'name' => 'java',
                'compile' => [
                    'src_name' => 'Main.java',
                    'exe_name' => 'Main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => -1,
                    'compile_command' => '/usr/bin/javac {src_path} -d {exe_dir} -encoding UTF8'
                ],
                'run' => [
                    'command' => '/usr/bin/java -cp {exe_dir} -XX:MaxRAM={max_memory}k -Djava.security.manager -Dfile.encoding=UTF-8 -Djava.security.policy==/etc/java_policy -Djava.awt.headless=true Main',
                    'seccomp_rule' => null,
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => [
                    'cpu' => ['factor' => 2, 'constant' => 1],
                    'memory' => ['factor' => 2, 'constant' => 16 * 1024 * 1024]
                ]
            ],
            'py2_lang_config' => [
                'compile' => [
                    'src_name' => 'solution.py',
                    'exe_name' => 'solution.pyc',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/python -m py_compile {src_path}',
                ],
                'run' => [
                    'command' => '/usr/bin/python {exe_path}',
                    'seccomp_rule' => 'general',
                    'env' => $default_env
                ],
                'factors' => [
                    'cpu' => ['factor' => 3, 'constant' => 2],
                    'memory' => ['factor' => 2, 'constant' => 32 * 1024 * 1024]
                ]
            ],
            'py3_lang_config' => [
                'compile' => [
                    'src_name' => 'solution.py',
                    'exe_name' => '__pycache__/solution.cpython-37.pyc',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/python3.7 -m py_compile {src_path}',
                ],
                'run' => [
                    'command' => '/usr/bin/python3.7 {exe_path}',
                    'seccomp_rule' => 'general',
                    'env' => array_merge(['MALLOC_ARENA_MAX=1', 'PYTHONIOENCODING=UTF-8'], $default_env)
                ],
                'factors' => [
                    'cpu' => ['factor' => 3, 'constant' => 2],
                    'memory' => ['factor' => 2, 'constant' => 32 * 1024 * 1024]
                ]
            ],
            'php7_lang_config' => [
                'compile' => null,
                'run' => [
                    'exe_name' => 'solution.php',
                    'command' => '/usr/bin/php -d error_reporting=0 -f {exe_path}',
                    'seccomp_rule' => null,
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => [
                    'cpu' => ['factor' => 1, 'constant' => 0],
                    'memory' => ['factor' => 1, 'constant' => 512 * 1024 * 1024]
                ]
            ],
            'php7_lang_config_spj' => [
                'compile' => [
                    'src_name' => 'spj-{spj_version}.php',
                    'exe_name' => 'spj-{spj_version}',
                    'max_cpu_time' => 3000 * 3,
                    'max_real_time' => 10000 * 3,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/bin/cp {src_path} {exe_path}'
                ],
                'run' => [
                    'exe_name' => 'spj-{spj_version}',
                    'command' => '/usr/bin/php -d error_reporting=0 -f {exe_path} {in_file_path} {out_file_path} {user_out_file_path}',
                    'seccomp_rule' => null,
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => [
                    'cpu' => ['factor' => 1, 'constant' => 0],
                    'memory' => ['factor' => 1, 'constant' => 512 * 1024 * 1024]
                ]
            ],
            'nodejs_lang_config' => [
                'compile' => null,
                'run' => [
                    'exe_name' => 'solution.js',
                    'command' => '/usr/bin/node --stack-size=65536 {exe_path}',
                    'seccomp_rule' => null,
                    'memory_limit_check_only' => 1
                ],
                'factors' => [
                    'cpu' => ['factor' => 3, 'constant' => 2],
                    'memory' => ['factor' => 2, 'constant' => 0]
                ]
            ],
            'go_lang_config' => [
                'compile' => [
                    'src_name' => 'main.go',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => -1,
                    'compile_command' => '/usr/bin/go build -o {exe_path} {src_path}',
                    'env' => ["GOCACHE=/tmp", "GOPATH=/root/go"]
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => "",
                    'env' => array_merge(["GODEBUG=madvdontneed=1", "GOCACHE=off"], $default_env),
                    'memory_limit_check_only' => 1
                ],
                'factors' => [
                    'cpu' => ['factor' => 1, 'constant' => 2],
                    'memory' => ['factor' => 1, 'constant' => 512 * 1024 * 1024]
                ]
            ],
            'csharp_lang_config' => [
                'compile' => [
                    'src_name' => 'main.cs',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/mcs -optimize+ -out:{exe_path} {src_path}'
                ],
                'run' => [
                    'command' => '/usr/bin/mono {exe_path}',
                    'seccomp_rule' => null,
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => [
                    'cpu' => ['factor' => 2, 'constant' => 1],
                    'memory' => ['factor' => 2, 'constant' => 16 * 1024 * 1024]
                ]
            ],
            'ruby_lang_config' => [
                'compile' => null,
                'run' => [
                    'exe_name' => 'solution.rb',
                    'command' => '/usr/bin/ruby {exe_path}',
                    'seccomp_rule' => null,
                    'memory_limit_check_only' => 1
                ],
                'factors' => [
                    'cpu' => ['factor' => 2, 'constant' => 1],
                    'memory' => ['factor' => 1, 'constant' => 512 * 1024 * 1024]
                ]
            ],
            'rust_lang_config' => [
                'compile' => [
                    'src_name' => 'main.rs',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/rustc -O -o {exe_path} {src_path}'
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => "general",
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => [
                    'cpu' => ['factor' => 1, 'constant' => 0],
                    'memory' => ['factor' => 1, 'constant' => 16 * 1024 * 1024]
                ]
            ],
            'haskell_lang_config' => [
                'compile' => [
                    'src_name' => 'main.hs',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/ghc -O -outputdir /tmp -o {exe_path} {src_path}'
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => "general",
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ],
                'factors' => $default_factors
            ],
            'pascal_lang_config' => [
                'compile' => [
                    'src_name' => 'main.pas',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/bin/fpc -O2 -o{exe_path} {src_path}'
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => "general",
                    'env' => $default_env
                ],
                'factors' => $default_factors
            ],
            'plaintext_lang_config' => [
                'compile' => null,
                'run' => [
                    'exe_name' => 'solution.txt',
                    'command' => '/bin/cat {exe_path}',
                    'seccomp_rule' => "general",
                    'memory_limit_check_only' => 1
                ],
                'factors' => $default_factors
            ],
            'basic_lang_config' => [
                'compile' => [
                    'src_name' => 'main.bas',
                    'exe_name' => 'main',
                    'max_cpu_time' => 3000,
                    'max_real_time' => 10000,
                    'max_memory' => 1024 * 1024 * 1024,
                    'compile_command' => '/usr/local/bin/fbc {src_path}'
                ],
                'run' => [
                    'command' => '{exe_path}',
                    'seccomp_rule' => "general",
                    'env' => $default_env
                ],
                'factors' => $default_factors
            ],
        ];
    }
}
