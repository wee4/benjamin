<?php
namespace Tests\Unit\Services\Gateways;

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Services\Gateways\BaseGateway;
use Ebanx\Benjamin\Services\Http\Client;
use Tests\TestCase;

class BaseGatewayTest extends TestCase
{
    public function testGatewayOnLiveMode()
    {
        $config = new Config(['isSandbox' => false]);
        $client = new Client();

        new TestGateway($config, $client);

        $this->assertEquals(Client::MODE_LIVE, $client->getMode());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowWithInvalidCountry()
    {
        $gateway = new TestGateway(new Config());
        $gateway->countryNotAvailable();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testEnabledCountriesGetterNotOverridden()
    {
        NoCountryNoCurrencyGateway::acceptsCountry('test');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testEnabledCurrenciesGetterNotOverridden()
    {
        NoCountryNoCurrencyGateway::acceptsCurrency('test');
    }
}

class NoCountryNoCurrencyGateway extends BaseGateway
{
    protected function getPaymentData(Payment $payment)
    {
        return;
    }
}

class TestGateway extends BaseGateway
{
    public function __construct(Config $config, Client $client = null)
    {
        $this->client = $client;
        parent::__construct($config);
    }

    public function countryNotAvailable()
    {
        $this->availableForCountryOrThrow('invalidCountry');
    }

    protected function getPaymentData(Payment $payment)
    {
        return;
    }

    protected static function getEnabledCountries()
    {
        return [];
    }

    protected static function getEnabledCurrencies()
    {
        return [];
    }
}