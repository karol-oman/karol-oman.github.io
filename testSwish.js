const fs = require('fs');
const https = require('https');
const axios = require('axios');
const { v4: createId } = require('uuid');

const agent = new https.Agent({
    cert: fs.readFileSync('./mss_2.0/__MACOSX/mss_test_1.9/Getswish_Test_Certificates/Swish_Merchant_TestCertificate_1234679304.pem', { encoding: 'utf8' }),
    key: fs.readFileSync('./mss_2.0/__MACOSX/mss_test_1.9/Getswish_Test_Certificates/Swish_Merchant_TestCertificate_1234679304.key', { encoding: 'utf8' }),
    ca: fs.readFileSync('./mss_2.0/__MACOSX/mss_test_1.9/Getswish_Test_Certificates/Swish_TLS_RootCA.pem', { encoding: 'utf8' }),
     passphrase: 'swish', 
});

async function createPaymentRequest(amount, message, payerAlias) {
    const instructionUUID = createId();
  
    const data = {
        payeeAlias: '1234679304',
        currency: 'SEK',
      callbackUrl: 'https://your-callback-url.com',
      amount,
      message,
      payerAlias
    };
  
    try {
      const response = await axios.put(
        `https://mss.cpc.getswish.net/swish-cpcapi/api/v2/paymentrequests/${instructionUUID}`,
        data,
        { httpsAgent: agent } // pass the configured agent here
      );
  
      if (response.status === 201) {
        return { id: instructionUUID };
      }
    } catch (error) {
      console.error(error);
    }
}

createPaymentRequest(1, '', '1234679304');

      

      
