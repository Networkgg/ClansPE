<?php

class PFConfig{
	/** @var string */
	public $clansDbType;
	/** @var array */
	public $clansDbMysqlDetails;
	/** @var array */
	public $clansDbSqliteDetails;

	/** @var string */
	public $authIntegrationType;

	public $plotSizeBits;

	public function __construct(Config $config){
		$this->clansDbType = $config->getNested("dataProviders.clans.type", "sqlite3");
		$this->clansDbMysqlDetails = $config->getNested("dataProviders.clans.mysql");
		$this->clansDbSqliteDetails = $config->getNested("dataProviders.clans.sqlite3");

		$this->authIntegrationType = $config->getNested("integrations.auth", "none");

		$plotSize = (int) $config->getNested("mechanism.plotSize", 8);
		if($plotSize === 0){
			$this->plotSizeBits = 3; // 2 << 3 => 8
		}else{
			for($i = 0; $i < PHP_INT_SIZE << 3; $i++){
				if(($plotSize >> $i) & 1){
					break;
				}
			}
			$bits = min(16, $i);
			$this->plotSizeBits = $bits;
		}
	}
}
