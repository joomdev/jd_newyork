<?php
/**
 * @package     VirtueMart.vmpayment
 * @subpackage  skrill
 *
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('Restricted access');

/**
 * SkrillPaymentCore class to create payment widget,
 * send payment request and receive payment response from payment gateway
 *
 * @package     VirtueMart.vmpayment
 * @subpackage  skrill
 * @since       1.0.0
 */
class SkrillPaymentCore
{
	/**
     * @var skrillPrepareUrl
     */
    protected static $skrillPrepareUrl = 'https://pay.skrill.com';

    /**
     * @var skrillQueryUrl
     */
    protected static $skrillQueryUrl = 'https://www.skrill.com/app/query.pl';

    /**
     * @var skrillRefundUrl
     */
    protected static $skrillRefundUrl = 'https://www.skrill.com/app/refund.pl';

    /**
     * @var skrillUrl
     */
    protected static $skrillUrl = 'www.skrill.com';

	/**
     * @var allowedCountries
     */
    public static $allowedCountries = array(
        'ALA','ALB','DZA','ASM','AND','AGO','AIA','ATA','ATG','ARG','ARM','ABW','AUS','AUT','AZE','BHS','BHR','BGD',
        'BRB','BLR','BEL','BLZ','BEN','BMU','BTN','BOL','BIH','BWA','BVT','BRA','BRN','BGR','BFA','BDI','KHM','CMR',
        'CAN','CPV','CYM','CAF','TCD','CHL','CHN','CXR','CCK','COL','COM','COG','COD','COK','CRI','CIV','HRV','CYP',
        'CZE','DNK','DJI','DMA','DOM','ECU','EGY','SLV','GNQ','ERI','EST','ETH','FLK','FRO','FJI','FIN','FRA','GUF',
        'PYF','ATF','GAB','GMB','GEO','DEU','GHA','GIB','GRC','GRL','GRD','GLP','GUM','GTM','GGY','HTI','HMD','VAT',
        'GIN','GNB','GUY','HND','HKG','HUN','ISL','IND','IDN','IRL','IMN','ISR','ITA','JAM','JPN','JEY','JOR','KAZ',
        'KEN','KIR','KOR','KWT','LAO','LVA','LBN','LSO','LBR','LIE','LTU','LUX','MAC','MKD','MDG','MWI','MYS','MDV',
        'MLI','MLT','MHL','MTQ','MRT','MUS','MYT','MEX','FSM','MDA','MCO','MNG','MNE','MSR','MAR','MOZ','MMR','NAM',
        'NPL','NLD','ANT','NCL','NZL','NIC','NER','NGA','NIU','NFK','MNP','NOR','OMN','PAK','PLW','PSE','PAN','PNG',
        'PRY','PER','PHL','PCN','POL','PRT','PRI','QAT','REU','ROU','RUS','RWA','SHN','KNA','LCA','MAF','SPM','VCT',
        'WSM','SMR','STP','SAU','SEN','SRB','SYC','SLE','SGP','SVK','SVN','SLB','SOM','ZAF','SGS','ESP','LKA','SUR',
        'SJM','SWZ','SWE','CHE','TWN','TJK','TZA','THA','TLS','TGO','TKL','TON','TTO','TUN','TUR','TKM','TCA','TUV',
        'UGA','UKR','ARE','GBR','USA','UMI','URY','UZB','VUT','VEN','VNM','VGB','VIR','WLF','ESH','YEM','ZMB','ZWE'
    );

    /**
     * @var unallowedCountries
     */
    public static $unallowedCountries = array(
        'AFG','CUB','ERI','IRN','IRQ','JPN','KGZ','LBY','PRK','SDN','SSD','SYR'
    );

    /**
	 * plugin method list
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	public static $paymentMethods = array(
		"skrill_apm" => array(
			"allowedCountries" => "ALL"
		),
		"skrill_wlt" => array(
			"allowedCountries" => "ALL"
		),
		"skrill_psc" => array(
            'allowedCountries'  => array(
                'ASM','AUT','BEL','CAN','HRV','CYP','CZE','DNK','FIN','FRA','DEU','GUM','HUN','IRL','ISL','ITA','LVA','LUX',
                'MLT','MEX','NLD','MNP','NOR','POL','PRT','PRI','PRY','ROU','SVK','SVN','ESP','SWE','CHE','TUR','GBR','USA',
                'VIR'
            )
        ),
		"skrill_pch" => array(
            'allowedCountries'  => array('AUT','BEL','CAN','CYP','CZE','DNK','FRA','GRC','HRV','HUN','IRL','ITA','LTU','LUX','MLT','NLD','POL','PRT','ROU','SVK','SVN','SWE','ESP','CHE','GBR')
        ),
		"skrill_acc" => array(
			"allowedCountries" => "ALL"
		),
		"skrill_vsa" => array(
			"allowedCountries" => "ALL"
		),
		"skrill_msc" => array(
			"allowedCountries" => "ALL"
		),
		"skrill_mae" => array(
            'allowedCountries'  => array('GBR','ESP','IRL','AUT')
        ),
		"skrill_obt" => array(
            'allowedCountries'  => array(
                'AUT','BEL','BGR','DNK','ESP','EST','FIN','FRA','DEU','HUN',
                'ITA','LVA','NLD','NOR','POL','PRT','SWE','GBR','USA', 'GRC'
            )
        ),
		"skrill_gir" => array(
            'allowedCountries'  => array('DEU')
        ),
		"skrill_ebt" => array(
            'allowedCountries'  => array('SWE')
        ),
		"skrill_npy" => array(
            'allowedCountries'  => array('AUT')
        ),
		"skrill_pli" => array(
            'allowedCountries'  => array('AUS')
        ),
		"skrill_pwy" => array(
            'allowedCountries'  => array('POL')
        ),
		"skrill_epy" => array(
            'allowedCountries'  => array('BGR')
        ),
		"skrill_ntl" => array(
            'allowedCountries'  => 'ALL',
            'exceptedCountries' => array(
                'AFG','ARM','BTN','BVT','MMR','CHN','COD','COK','CUB','ERI','SGS','GUM','GIN','HMD','IRN','IRQ','CIV',
                'KAZ','PRK','KGZ','LBR','LBY','MNG','MNP','FSM','MHL','PLW','PAK','TLS','PRI','SLE','SOM','ZWE','SDN',
                'SYR','TJK','TKM','UGA','USA','VIR','UZB','YEM'
            )
        ),
		"skrill_ali" => array(
            'allowedCountries'  => array('CHN','GBR')
        ),
		"skrill_adb" => array(
            'name' => 'Direct Bank Transfer',
            'allowedCountries' => array(
                'ARG' => array(
                    'Banco Santander Rio' => 'santander-rio.png'
                ),
                'BRA' => array(
                    'Banco Itau' => 'itau.png',
                    'Banco do Brasil' => 'banco-do-brasil.png',
                    'Banco Bradesco' => 'bradesco.png'
                )
            )
        ),
		"skrill_aob" => array(
            'name' => 'Manual Bank Transfer',
            'allowedCountries' => array(
                'BRA'=> array(
                    'HSBC' =>'hsbc.png',
                    'Caixa' => 'caixa.png',
                    'Santander' => 'santander.png'
                ),
                'CHL' => array(
                    'WebPay' => 'webpaylogo.png'
                ),
                'COL' => array(
                    'Bancolombia' => 'bancolombia.jpg',
                    'PSEi' => 'PSEi.png'
                )
            )
        ),
		"skrill_aci" => array(
            'name' => 'Cash / Invoice',
            'allowedCountries' => array(
                'ARG' => array(
                    'RedLink' => 'red-link.png',
                    'Pago Facil' => 'pago-facil.png'
                ),
                'BRA' => array(
                    'Boleto Bancario' => 'boleto-bancario.png'
                ),
                'CHL' => array(
                    'Servi Pag' => 'servi-pag.png'
                ),
                'COL' => array(
                    'Efecty' => 'efecty.png',
                    'Davivienda' => 'davivienda.png',
                    'Éxito' => 'exito.png',
                    'Carulla' => 'carulla.png',
                    'EDEQ' => 'edeq.png',
                    'SurtiMax' => 'surtimax.png'
                ),
                'MEX' => array(
                    'OXXO' => 'oxxo.png',
                    'BBVA Bancomer' => 'bancomer_m.png',
                    'Banamex' => 'banamex.png',
                    'Banco Santander' => 'santander.png',
                ),
                'PER' => array(
                    'Banco de Occidente' => 'banco-de-occidente.png'
                ),
                'URY' => array(
                    'Redpagos' => 'red-pagos.png'
                )
            ),
        ),
		"skrill_aup" => array(
            'allowedCountries' => array('CHN')
        ),
		"skrill_btc" => array(
            'allowedCountries' => 'ALL',
            'exceptedCountries' => array('CUB','SDN','SYR','PRK','IRN','KGZ','BOL','ECU','BGD','CAN','USA','TUR')
        ),
        "skrill_idl" => array(
            'allowedCountries'  => array(
                'NLD'
            )
        )
	);

    /**
     * Get Sid from Skrill gateway by request parameters
     *
     * @param array $fields
     * @return string
     */
    public static function getSid($fields)
    {
        $fields_string = http_build_query($fields);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::$skrillPrepareUrl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'));
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($curl, CURLOPT_POST, count($fields));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);

            $result = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new Exception("Curl error: ". curl_error($curl));
        }
        curl_close($curl);

        return $result;
    }

    /**
     * parser response
     *
     * @param json $response
     * @return array
     */
    protected static function _parse_response ($response) {

		$matches = array();
		$rlines = explode ("\r\n", $response);

		foreach ($rlines as $line) {
			if (preg_match ('/([^:]+): (.*)/im', $line, $matches)) {
				continue;
			}

			if (preg_match ('/([0-9a-f]{32})/im', $line, $matches)) {
				return $matches;
			}
		}

		return $matches;
	}

    /**
     * Get supported payments by country code.
     * check if country code in allowed countries and exclude disallowed countries and excepted countries.
     *
     * @param string $countryCode
     * @return array
     */
    public static function getSupportedPaymentsByCountryCode($countryCode)
    {
        $supportedPayments = array();

        if (!in_array($countryCode, self::$unallowedCountries)) {
            foreach (self::$paymentMethods as $key => $paymentMethod) {
                if (isset($paymentMethod['exceptedCountries'])
                    && in_array($countryCode, $paymentMethod['exceptedCountries'])
                ) {
                    continue;
                }
                if ($paymentMethod['allowedCountries'] == 'ALL') {
                    $paymentMethod['allowedCountries'] = self::$allowedCountries;
                }
                $paymentMethodKey = strtoupper($key);
                $paymenBrand = str_replace('SKRILL_', '', $paymentMethodKey);
                if ($paymenBrand == 'AOB' || $paymenBrand == 'ADB' || $paymenBrand == 'ACI') {
                    if (in_array($countryCode, array_keys($paymentMethod['allowedCountries']))) {
                        $supportedPayments[] =  $key;
                    }
                } else {
                    if (in_array($countryCode, $paymentMethod['allowedCountries'])) {
                        $supportedPayments[] =  $key;
                    }
                }
            }
        }
        return $supportedPayments;
    }

    /**
     * Post to Skrill Gateway
     *
     * @param : $url, $postParameters
     * @return : string
     */
    private static function post($url, $postParameters)
    {
        $postFields = http_build_query($postParameters, '', '&');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'));
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($curl, CURLOPT_POST, count($postParameters));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return false;
        }
        curl_close($curl);

        return $result;
    }

    /**
     * Get Skrill Payment Url by checkout parameters
     *
     * @param string $sid
     * @return string | boolean
     */
    public static function getSkrillPaymentUrl($sid)
    {
        $skrillPaymentUrl = self::$skrillPrepareUrl.'?sid='.$sid;
        return $skrillPaymentUrl;
    }

    /**
     * Return true if sid is valid md5 format
     *
     * @param  string $sid
     * @return boolean
     */
    public static function isSidValid($sid = '')
    {
        if (preg_match('/^[a-f0-9]{32}$/', $sid)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Skrill Refund
     *
     * @param : $action(prepare/refund), $post_variables
     * @return : array
     */
    public static function doRefund($post_variables)
    {
        $skrillRefundUrl = self::$skrillRefundUrl;

        $prepareParameters = $post_variables;
        $prepareParameters['action'] = 'prepare';

        $parametersLog = $prepareParameters;
        $parametersLog['password'] = '*****';

        $postResult = self::post($skrillRefundUrl, $prepareParameters);
        $prepareResult = simplexml_load_string($postResult);
        $error = (string) $prepareResult->error->error_msg;

        if (!empty($error)) {
            if ($error == 'CANNOT_LOGIN') {
                return $error;
            }
            if (strpos($error, 'LOCK') !== false) {
                return 'ACCOUNT_LOCKED';
            }
            return 'GENERAL_ERROR';
        }

        $sid = (string)$prepareResult->sid;

        if (isset($sid)) {
            $refundParameters['action'] = 'refund';
            $refundParameters['sid'] = $sid;
            $postResult = self::post($skrillRefundUrl, $refundParameters);
            return simplexml_load_string($postResult);
        }

        return 'GENERAL_ERROR';
    }
}
