<?php
namespace App\Babel\Extension\noj;

class Languages
{
    public static function get()
    {
        $default_env=["LANG=en_US.UTF-8", "LANGUAGE=en_US:en", "LC_ALL=en_US.UTF-8"];
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
                ]
            ],
            'c_lang_spj_compile' => [
                'src_name' => 'spj-{spj_version}.c',
                'exe_name' => 'spj-{spj_version}',
                'max_cpu_time' => 3000,
                'max_real_time' => 10000,
                'max_memory' => 1024 * 1024 * 1024,
                'compile_command' => '/usr/bin/gcc -DONLINE_JUDGE -O2 -w -fmax-errors=3 -std=c99 {src_path} -lm -o {exe_path}'
            ],
            'c_lang_spj_config' => [
                'exe_name' => 'spj-{spj_version}',
                'command' => '{exe_path} {in_file_path} {user_out_file_path}',
                'seccomp_rule' => 'c_cpp',
                'env' => $default_env
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
                    'seccomp_rule' => 'c_cpp',
                    'env' => $default_env,
                    'memory_limit_check_only' => 1
                ]
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
                ]
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
                ]
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
                ]
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
                ]
            ],
            'jsc_lang_config' => [
                'compile' => null,
                'run' => [
                    'exe_name' => 'solution.js',
                    'command' => '/usr/bin/jsc {exe_path}',
                    'seccomp_rule' => null,
                    'memory_limit_check_only' => 1
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
                ]
            ],
            'ruby_lang_config' => [
                'compile' => null,
                'run' => [
                    'exe_name' => 'solution.rb',
                    'command' => '/usr/bin/ruby {exe_path}',
                    'seccomp_rule' => null,
                    'memory_limit_check_only' => 1
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
                ]
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
                ]
            ],
            'plaintext_lang_config' => [
                'compile' => null,
                'run' => [
                    'exe_name' => 'solution.txt',
                    'command' => '/bin/cat {exe_path}',
                    'seccomp_rule' => "general",
                    'memory_limit_check_only' => 1
                ]
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
                ]
            ],
        ];
    }
}
