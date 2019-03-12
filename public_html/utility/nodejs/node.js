const https = require('https');
const food = 'rice';

function httpGet() {
  return new Promise(((resolve, reject) => {
    var options = {
        host: 'api.icndb.com',
        port: 443,
        path: '/jokes/random',
        method: 'GET',
    };

    const request = https.request(options, (response) => {
      response.setEncoding('utf8');
      let returnData = '';

      response.on('data', (chunk) => {
        returnData += chunk;
      });

      response.on('end', () => {
        resolve(JSON.parse(returnData));
      });

      response.on('error', (error) => {
        reject(error);
      });
    });
    request.end();
  }));
}

const response = await httpGet();
console.log(response);

console.log('end');
