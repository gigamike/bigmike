<?php

namespace Product\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetQRCode extends AbstractHelper
{
	private $_sm;

  public function __construct(\Zend\ServiceManager\ServiceManager $sm) {
    $this->_sm = $sm;
  }

  public function getSm() {
    return $this->_sm;
  }

	public function __invoke($product_id, $width, $height)
	{
		$config = $this->getSm()->get('Config');
		$productUrl = $config['baseUrl'] . "product/view/" . $product_id;
		$url = "https://chart.googleapis.com/chart?chs=" . $width . "x" . $height . "&cht=qr&chl=" . urlencode($productUrl) . "&choe=UTF-8";

	 	return $url;
	}
}
