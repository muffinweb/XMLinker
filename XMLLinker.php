<?php

/**
 * GIB (Gelir Idaresi Baskanligi) XMLto HTML Previewer
 * UBL-TR
 *
 * @package XMLLinker
 * @author Ugur Cengiz <ugurcengiz@mail.com.tr>
 * @version 1.0.0
 */

class XMLinker
{
	public static function preview($xmlFilePath = null, $returnable = false){

		/**
		 * Reading XMLContent
		 */
        if(!$xml = @file_get_contents($xmlFilePath)){
            return 'xmlyok';
        }

        //Getting rid of .NET related addons from tags
        $xml = str_replace('ds:', '', $xml);
        $xml = str_replace('ext:', '', $xml);
        $xml = str_replace('xades:', '', $xml);
        $xml = str_replace('cbc:', '', $xml);
        $xml = str_replace('cac:', '', $xml);

        //Converting datas to typeOf Object
        $xmlData = simplexml_load_string(trim($xml));
        $xmlData = json_decode(json_encode($xmlData));


        //Getting XSLT Data
        if(is_object($xmlData->AdditionalDocumentReference)){
            $xsl = base64_decode($xmlData->AdditionalDocumentReference->Attachment->EmbeddedDocumentBinaryObject);
        }

        if(is_array($xmlData->AdditionalDocumentReference)){
            foreach($xmlData->AdditionalDocumentReference as $additionalRef){
                if(isset($additionalRef->DocumentType)){
                    if(gettype($additionalRef->DocumentType) == 'string'){
                        if(strtolower($additionalRef->DocumentType) == 'xslt'){
                            $xsl = base64_decode($additionalRef->Attachment->EmbeddedDocumentBinaryObject);
                        }
                    }
                }
            }
        }

        //We need temp folder to use xlst file extracted

        if(!is_dir('xmllinkerTemp')){
        	mkdir('xmllinkerTemp', 0755);
        }

        $tempfile = tempnam("xmllinkerTemp", "xls");
        $readtempfile = fopen($tempfile, "w+");
        fwrite($readtempfile, $xsl);




        // Lets' load XML
        $xml = new DOMDocument;
        $xml->load($xmlFilePath);

        //Let's load XSLT File
        $xsl = new DOMDocument;
        $xsl->load($tempfile);


        //Let's initialize XSLTProcessor & load xslt data
        $proc = new XSLTProcessor;
        @$proc->importStyleSheet($xsl); //Xslt rules here


        // Let's create HTMLView from xmldata with xlst-loaded XSLTProcessor
        // If arguman 2 is true data will be returned, else buffer output
        if($returnable){
            return $proc->transformToXML($xml);
        }else{
            echo $proc->transformToXML($xml);
        }

        //Close temp reading process
        fclose($readtempfile);

        //Lastly delete tempfile created recently
        unlink($tempfile);
    }
}