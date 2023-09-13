<?php


namespace Encore\OrgRbac\Services;


use function PHPUnit\Framework\isNull;

class SnowFlakeService implements DatabasePrimaryKeyGenerateDriver
{
    public static $instance = null;
    private $runTime;//项目开始时间 1689091200
    private $dataCenterIdBits;
    private $workerIdBits; // 机器标识位数
    const SEQUENCE_BITS      = 12; // 毫秒内自增位
    private $max12bit = -1 ^ (-1 << self::SEQUENCE_BITS);
    private $max41bit;
    static $machineId = null;
    static $centerDataId = null;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    private function __construct($dataCenterIdBits,$workerIdBits)
    {
        $this->dataCenterIdBits = $dataCenterIdBits;
        $this->workerIdBits = $workerIdBits;
        $this->runTime = env('runTime',1689091200000);
        $this->max41bit = env('keyTime',1593532800000);
    }

    public static function load($dataCenterIdBits = 5,$workerIdBits = 5)
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($dataCenterIdBits,$workerIdBits);
        }
        return self::$instance;
    }

    public function __get($name)
    {
        if (strtolower($name) == 'id') {
            return $this->createId();
        }
    }

    private function createId(){
        //现在的毫秒时间戳 - 42 bits
        $time = floor(microtime(true) * 1000);
        //现在的时间减去指定的时间
        $time -= $this->runTime;

        //创建一个基础时间
        $base = decbin($this->max41bit + $time);

        //配置机器id,与 数据中心id 一共10 bit 最多可以有1024台机器(为配置情况下,自动填充)
        $machineId = self::$machineId;
        $centerDataId = self::$centerDataId;
        if(isNull($machineId)) {
            $machineId = str_pad(decbin(self::$machineId), $this->workerIdBits, "0", STR_PAD_LEFT);
        }
        if(isNull($centerDataId)) {
            $centerDataId = str_pad(decbin(self::$centerDataId), $this->dataCenterIdBits, "0", STR_PAD_LEFT);
        }

        // 序列号 12bits 最多可以有4096个随机数 #TODO 这块应该实现一个毫秒内递增
        $random = str_pad(decbin(mt_rand(0, $this->max12bit)), self::SEQUENCE_BITS, "0", STR_PAD_LEFT);

        //连接
        $suffix = 0;
        $base = $suffix.$base.$centerDataId.$machineId.$random;
        //返回唯一id
        return bindec($base);
    }

    public function generate()
    {
        return $this->createId();
    }
}
