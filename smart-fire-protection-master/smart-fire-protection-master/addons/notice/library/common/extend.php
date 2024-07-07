<?php

namespace hnh\custom {

    class Log
    {

        /**
         * 记录信息
         * @param         $title
         * @param  array  $extend
         */
        static public function record($title,  $extend = [])
        {
            $data = [
                'title' => $title
            ];

            $data = array_merge($data, $extend);

            \think\Log::write($data, 'notice' ,true);
        }


        /**
         * 记录异常错误信息
         * @param         $msg string|array 错误信息
         */
        static public function error($msg)
        {
            if (is_array($msg)) {
                $msg = var_export($msg, true);
            }


            \think\Log::write($msg, 'error' ,true);
        }

        /**
         * 记录异常错误信息
         * @param         $title
         * @param  null   $e
         * @param  array  $extend
         */
        static public function catch($title, $e = null, $extend = [])
        {
            $data = [
                'title' => $title
            ];

            if ($e instanceof \Exception) {
                $data['exception'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                ];
            }

            $data = array_merge($data, $extend);

            if (is_array($data)) {
                $data = var_export($data, true);
            }

            \think\Log::write($data, 'error' ,true);
        }

        /**
         * Logs with an arbitrary level.
         *
         * @param mixed   $level
         * @param string  $message
         * @param mixed[] $context
         *
         * @return void
         *
         * @throws \Psr\Log\InvalidArgumentException
         */
        public function log($level, $message, array $context = array())
        {
            \think\Log::write($message);
        }
    }
}

