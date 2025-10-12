<?php

namespace Tests\Unit;

use App\Libraries\Account;
use App\Libraries\Bank;
use App\Libraries\Finance;
use App\Libraries\Login;
use App\Libraries\OFX;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class OFXTest extends TestCase
{
    private Finance $finance;
    private Bank $bank;

    protected function setUp(): void
    {
        parent::setUp();
        $this->finance = new Finance();
        $this->bank = new Bank($this->finance, 'TEST_FID', 'https://test.example.com/ofx', 'TEST_ORG');
    }

    #[Test]
    public function itConstructsOfxObjectWithBankAndRequest(): void
    {
        $this->markTestIncomplete();
        
        $request = "TEST_REQUEST_DATA";
        $ofx = new OFX($this->bank, $request);

        $this->assertInstanceOf(OFX::class, $ofx);
        $this->assertSame($this->bank, $ofx->bank);
        $this->assertSame($request, $ofx->request);
    }

    #[Test]
    public function itClosesTagsWithSingleTag(): void
    {
        $this->markTestIncomplete();
        
        $input = '<TAG>value';
        $expected = '<TAG>value</TAG>';
        $ofx = new OFX($this->bank, '');
        $result = $ofx->closeTags($input);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function itClosesTagsWithMultipleUnclosedTags(): void
    {
        $this->markTestIncomplete();
        
        $input = '<TAG1>value1<TAG2>value2';
        $expected = '<TAG1>value1</TAG1><TAG2>value2</TAG2>';
        $ofx = new OFX($this->bank, '');
        $result = $ofx->closeTags($input);

        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function itClosesTagsWithWhitespace(): void
    {
        $this->markTestIncomplete();
        
        $input = '<TAG>  value  with  spaces  ';
        $ofx = new OFX($this->bank, '');
        $result = $ofx->closeTags($input);

        $this->assertStringNotContainsString('  ', $result);
    }

    #[Test]
    public function itDoesNotCloseAlreadyClosedTags(): void
    {
        $this->markTestIncomplete();
        
        $input = '<TAG>value</TAG>';
        $ofx = new OFX($this->bank, '');
        $result = $ofx->closeTags($input);

        $this->assertEquals($input, $result);
    }

    #[Test]
    public function itHandlesEmptyStringInCloseTags(): void
    {
        $this->markTestIncomplete();
        
        $ofx = new OFX($this->bank, '');
        $result = $ofx->closeTags('');

        $this->assertEquals('', $result);
    }

    #[Test]
    public function itHandlesNestedTagsCorrectly(): void
    {
        $this->markTestIncomplete();
        
        $input = '<OUTER><INNER>value';
        $ofx = new OFX($this->bank, '');
        $result = $ofx->closeTags($input);

        $this->assertStringContainsString('</INNER>', $result);
        $this->assertStringContainsString('</OUTER>', $result);
    }

    #[Test]
    public function itProcessesXmlFromResponseBody(): void
    {
        $this->markTestIncomplete();
        
        $ofx = new OFX($this->bank, 'request');
        $ofx->responseBody = '<OFX><TAG>value</TAG></OFX>';

        $xml = $ofx->xml();

        $this->assertInstanceOf(SimpleXMLElement::class, $xml);
    }

    #[Test]
    public function itHandlesMalformedXmlGracefully(): void
    {
        $this->markTestIncomplete();
        
        $ofx = new OFX($this->bank, 'request');
        $ofx->responseBody = '<OFX><TAG>value';

        $this->expectException(\Exception::class);
        $ofx->xml();
    }

    #[Test]
    public function itSplitsResponseHeaderAndBody(): void
    {
        $this->markTestIncomplete();
        
        $ofx = new OFX($this->bank, 'request');
        $header = "OFXHEADER:100\nDATA:OFXSGML\n";
        $body = "<OFX><CONTENT>data</CONTENT></OFX>";
        $ofx->response = $header . $body;

        // Simulate the splitting logic from go() method
        $tmp = explode('<OFX>', $ofx->response);
        $responseHeader = $tmp[0];
        $responseBody = '<OFX>' . $tmp[1];

        $this->assertEquals($header, $responseHeader);
        $this->assertEquals($body, $responseBody);
    }
}