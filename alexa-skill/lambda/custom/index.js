/* eslint-disable  func-names */
/* eslint-disable  no-console */

const Alexa = require('ask-sdk-core');
const request = require('request');
const https = require('https');
const await = require('await');
const Promise = require('promise');

function getFood(food) {
  return new Promise(((resolve, reject) => {
    var options = {
        host: 'aws2019.gigamike.net',
        port: 443,
        path: '/api/nutrition-fact?food=' + food,
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

const LaunchRequestHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'LaunchRequest';
  },
  handle(handlerInput) {
    const speechText = 'Welcome to the Big Mike, your personal fitness coach by team Gigamike. Do you want to check if your weight is healthy? just say get my body mass index. Or ask about food nutrition facts. Example, by saying nutrition facts for rice. Or order supplements online by saying, add Enervon C.';
    const repromptText = 'Do you want to check if your weight is healthy? just say get body mass index. Or ask about food nutrition facts. Example, by saying nutrition facts for rice. Or order supplements or accessories online by linking your account and saying, add Enervon C to my cart.';

    return handlerInput.responseBuilder
      .speak(speechText)
      .reprompt(speechText)
      .withSimpleCard('Welcome to the Big Mike, your personal fitness coach.', speechText)
      .getResponse();
  },
};

const GetBodyMassIndexIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetBodyMassIndexIntent';
  },
  handle(handlerInput) {
    const speechText = 'What is your height in centimeters? Just say my height is';
    const repromptText = 'What is your height in centimeters? Just say my height is';

    return handlerInput.responseBuilder
      .speak(speechText)
      .reprompt(speechText)
      .withSimpleCard('Get height in centimeters', speechText)
      .getResponse();
  },
};

const GetBodyMassIndexHeightIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetBodyMassIndexHeightIntent';
  },
  handle(handlerInput) {
    const attributes = handlerInput.attributesManager.getSessionAttributes();
    const heightCentimeters = handlerInput.requestEnvelope.request.intent.slots.HeightCentimeters.value;
    attributes.heightCentimeters = heightCentimeters;

    const speechText = 'You are ' + heightCentimeters + ' centimeters in height. What is your weight in kilograms? Just say my weight is';
    const repromptText = 'What is your weight in kilograms? Just say my weight is';

    return handlerInput.responseBuilder
      .speak(speechText)
      .reprompt(speechText)
      .withSimpleCard('Get weight in kilograms', speechText)
      .getResponse();
  },
};

const GetBodyMassIndexWeightIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetBodyMassIndexWeightIntent';
  },
  handle(handlerInput) {
    const attributes = handlerInput.attributesManager.getSessionAttributes();
    const weightKilograms = handlerInput.requestEnvelope.request.intent.slots.WeightKilograms.value;
    attributes.weightKilograms = weightKilograms;

    var speechText = '';
    const repromptText = '';
    var bmiResut = '';

    if(attributes.heightCentimeters > 0 && attributes.weightKilograms > 0){
      var bmi = attributes.weightKilograms / (attributes.heightCentimeters / 100 * attributes.heightCentimeters / 100);

      if(bmi < 18.5){
        bmiResut = "Your B M I category is underweight.";
        speechText += 'You are ' + weightKilograms + ' kilograms in weight. ' + bmiResut + ".";
        speechText += 'I can recommend a diet for underweight by saying diet for underweight. Or I can recommend a gym program for underweight by saying gym program for underweight.';
      }else if(bmi >= 18.5 && bmi <= 24.9){
        bmiResut = "Your B M I category is normal weight."
        speechText += 'You are ' + weightKilograms + ' kilograms in weight. ' + bmiResut + ".";
        speechText += 'Congratulations! Your physically fit. I can recommend a diet to maintain your normal weight by saying diet for normal weight. Or I can recommend a gym program to maintain your normal weight by saying gym program for normal weight.';
      }else if(bmi >= 25 && bmi <= 29.9){
        bmiResut = "Your B M I category is overweight."
        speechText += 'You are ' + weightKilograms + ' kilograms in weight. ' + bmiResut + ".";
        speechText += 'I can recommend a diet for underweight by saying diet for overweight. Or I can recommend a gym program for underweight by saying gym program for overweight.';
      }else if(bmi >= 30){
        bmiResut = "Your B M I category is obese."
        speechText += 'You are ' + weightKilograms + ' kilograms in weight. ' + bmiResut + ".";
        speechText += 'I can recommend a diet for underweight by saying diet for obese. Or I can recommend a gym program for obese by saying gym program for obese.';
      }
    }else{
      bmiResut = "Invalid entries!"
      speechText += bmiResut;
    }

    return handlerInput.responseBuilder
      .speak(speechText)
      .reprompt(speechText)
      .withSimpleCard('Get weight in kilograms', speechText)
      .getResponse();
  },
};

const GetDietForUnderweightIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetDietForUnderweightIntent';
  },
  handle(handlerInput) {
    var speechText = `Here is an example of a diet that will provide you with sufficient energy to assist with weight gain. `;
    speechText += `Foods that should be included every day.<break time="2s"/>`;
    speechText += `Full cream milk 750 to 1000 ml or 3 to 4 cups.<break time="2s"/>`;
    speechText += `Meat, fish, eggs and other protein foods 3 to 5 servings or 90 to 150 grams.<break time="2s"/>`;
    speechText += `Bread and cereals 8 to 12 servings e.g. up to 6 cups of starch a day.<break time="2s"/>`;
    speechText += `Fruit and vegetables 3to 5 servings.<break time="2s"/>`;
    speechText += `Fats and oils 90 grams 6 tablespoons.<break time="2s"/>`;
    speechText += `or Healthy desserts 1 to 2 servings`;

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Diet For Underweight', speechText)
      .getResponse();
  },
};

const GetDietForNormalWeightIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetDietForNormalWeightIntent';
  },
  handle(handlerInput) {
    var speechText = `Here is an example of a diet that will provide you with sufficient energy to assist with weight gain. `;
    speechText += `Foods that should be included every day.<break time="2s"/>`;
    speechText += `Full cream milk 750 to 1000 ml or 3 to 4 cups.<break time="2s"/>`;
    speechText += `Meat, fish, eggs and other protein foods 3 to 5 servings or 90 to 150 grams.<break time="2s"/>`;
    speechText += `Bread and cereals 8 to 12 servings e.g. up to 6 cups of starch a day.<break time="2s"/>`;
    speechText += `Fruit and vegetables 3to 5 servings.<break time="2s"/>`;
    speechText += `Fats and oils 90 grams 6 tablespoons.<break time="2s"/>`;
    speechText += `or Healthy desserts 1 to 2 servings`;

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Diet For Normal Weight', speechText)
      .getResponse();
  },
};

const GetDietForOverweightIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetDietForOverweightIntent';
  },
  handle(handlerInput) {
    var speechText = `Here is an example of a diet that will provide you with sufficient energy to assist with weight gain. `;
    speechText += `Foods that should be included every day.<break time="2s"/>`;
    speechText += `Full cream milk 750 to 1000 ml or 3 to 4 cups.<break time="2s"/>`;
    speechText += `Meat, fish, eggs and other protein foods 3 to 5 servings or 90 to 150 grams.<break time="2s"/>`;
    speechText += `Bread and cereals 8 to 12 servings e.g. up to 6 cups of starch a day.<break time="2s"/>`;
    speechText += `Fruit and vegetables 3to 5 servings.<break time="2s"/>`;
    speechText += `Fats and oils 90 grams 6 tablespoons.<break time="2s"/>`;
    speechText += `or Healthy desserts 1 to 2 servings`;

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Diet For Overweight', speechText)
      .getResponse();
  },
};

const GetDietForObeseIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetDietForObeseIntent';
  },
  handle(handlerInput) {
    var speechText = `Here is an example of a diet that will provide you with sufficient energy to assist with weight gain. `;
    speechText += `Foods that should be included every day.<break time="2s"/>`;
    speechText += `Full cream milk 750 to 1000 ml or 3 to 4 cups.<break time="2s"/>`;
    speechText += `Meat, fish, eggs and other protein foods 3 to 5 servings or 90 to 150 grams.<break time="2s"/>`;
    speechText += `Bread and cereals 8 to 12 servings e.g. up to 6 cups of starch a day.<break time="2s"/>`;
    speechText += `Fruit and vegetables 3to 5 servings.<break time="2s"/>`;
    speechText += `Fats and oils 90 grams 6 tablespoons.<break time="2s"/>`;
    speechText += `or Healthy desserts 1 to 2 servings`;

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Diet For Obese', speechText)
      .getResponse();
  },
};

const GetGymProgramForUnderweightIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetGymProgramForUnderweightIntent';
  },
  handle(handlerInput) {
    handlerInput.responseBuilder.addVideoAppLaunchDirective("https://s3.amazonaws.com/gigamike/exercise.mp4", "Momoland", "boomboom");
    return handlerInput.responseBuilder.speak("Gym Program For Underweight").getResponse();
  },
};

const GetGymProgramForNormalWeightIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetGymProgramForNormalWeightIntent';
  },
  handle(handlerInput) {
    handlerInput.responseBuilder.addVideoAppLaunchDirective("https://s3.amazonaws.com/gigamike/exercise.mp4", "Gym Program For Normal Weight", "Gym Program For Normal Weight");
    return handlerInput.responseBuilder.speak("Gym Program For Normal Weight").getResponse();
  },
};

const GetGymProgramForOverweightIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetGymProgramForOverweightIntent';
  },
  handle(handlerInput) {
    handlerInput.responseBuilder.addVideoAppLaunchDirective("https://s3.amazonaws.com/gigamike/exercise.mp4", "Gym Program For Overweight", "Gym Program For Overweight");
    return handlerInput.responseBuilder.speak("Gym Program For Overweight").getResponse();
  },
};

const GetGymProgramForObeseIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetGymProgramForObeseIntent';
  },
  handle(handlerInput) {
    handlerInput.responseBuilder.addVideoAppLaunchDirective("https://s3.amazonaws.com/gigamike/exercise.mp4", "Gym Program For Obese", "Gym Program For Obese");
    return handlerInput.responseBuilder.speak("Gym Program For Obese").getResponse();
  },
};

const GetNutritionFactIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetNutritionFactIntent';
  },
  async handle(handlerInput) {
    const food = handlerInput.requestEnvelope.request.intent.slots.Food.value;
    const response = await getFood(food);
    const speechText = response.text;

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Nutrition Facts for ' + food, speechText)
      .getResponse();
  },
};

const GetAddCartEnervonCIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetAddCartEnervonCIntent';
  },
  handle(handlerInput) {
    var speechText = 'Enervon C added to cart';

    var url = `https://aws2019.gigamike.net/api/cart-add?user_id=10&product_id=18&quantity=1`;
    request.get(url, (error, response, body) => {

    });

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Add Enervon C to cart', speechText)
      .getResponse();
  },
};

const GetAddCartOptimumNutritionWheyProteinIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetAddCartOptimumNutritionWheyProteinIntent';
  },
  handle(handlerInput) {
    var speechText = 'Optimum Nutrition (ON) Gold Standard 100% Whey Protein Powder - 5 lbs, 2.27 kg (Double Rich Chocolate) added to cart';

    var url = `https://aws2019.gigamike.net/api/cart-add?user_id=10&product_id=15&quantity=1`;
    request.get(url, (error, response, body) => {

    });

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Add Optimum Nutrition (ON) Gold Standard 100% Whey Protein Powder - 5 lbs, 2.27 kg (Double Rich Chocolate) to cart', speechText)
      .getResponse();
  },
};

const GetAddCartCentrumMultivitaminIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetAddCartCentrumMultivitaminIntent';
  },
  handle(handlerInput) {
    var speechText = 'Centrum Multivitamin for Adults added to cart';

    var url = `https://aws2019.gigamike.net/api/cart-add?user_id=10&product_id=16&quantity=1`;
    request.get(url, (error, response, body) => {

    });

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Add Centrum Multivitamin for Adults to cart', speechText)
      .getResponse();
  },
};

const GetAddCartMuscleTechCreatineIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetAddCartMuscleTechCreatineIntent';
  },
  handle(handlerInput) {
    var speechText = 'MuscleTech Platinum Creatine Monohydrate Powder, 14.1oz (80 Servings) added to cart';

    var url = `https://aws2019.gigamike.net/api/cart-add?user_id=10&product_id=20&quantity=1`;
    request.get(url, (error, response, body) => {

    });

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Add MuscleTech Platinum Creatine Monohydrate Powder, 14.1oz (80 Servings) to cart', speechText)
      .getResponse();
  },
};

const GetAddCartHydroxycutHardcoreIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetAddCartHydroxycutHardcoreIntent';
  },
  handle(handlerInput) {
    var speechText = 'Hydroxycut Hardcore Elite added to cart';

    var url = `https://aws2019.gigamike.net/api/cart-add?user_id=10&product_id=21&quantity=1`;
    request.get(url, (error, response, body) => {

    });

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Add Hydroxycut Hardcore Elite to cart', speechText)
      .getResponse();
  },
};

const GetAddCartOptimumNutritionLGlutamineIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetAddCartOptimumNutritionLGlutamineIntent';
  },
  handle(handlerInput) {
    var speechText = 'Optimum Nutrition L-Glutamine Capsules added to cart';

    var url = `https://aws2019.gigamike.net/api/cart-add?user_id=10&product_id=22&quantity=1`;
    request.get(url, (error, response, body) => {

    });

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Add Optimum Nutrition L-Glutamine Capsules to cart', speechText)
      .getResponse();
  },
};

const GetAddCartAnimalPakMultivitaminIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetAddCartAnimalPakMultivitaminIntent';
  },
  handle(handlerInput) {
    var speechText = 'Animal Pak Multivitamin 30 Paks added to cart';

    var url = `https://aws2019.gigamike.net/api/cart-add?user_id=10&product_id=19&quantity=1`;
    request.get(url, (error, response, body) => {

    });

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Add Animal Pak Multivitamin 30 Paks to cart', speechText)
      .getResponse();
  },
};

const GetAddCartSchiekWristWrapsIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'GetAddCartSchiekWristWrapsIntent';
  },
  handle(handlerInput) {
    var speechText = 'Schiek Black Line Heavy Duty Wrist Wraps added to cart';

    var url = `https://aws2019.gigamike.net/api/cart-add?user_id=10&product_id=17&quantity=1`;
    request.get(url, (error, response, body) => {

    });

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Add Schiek Black Line Heavy Duty Wrist Wraps to cart', speechText)
      .getResponse();
  },
};

const HelpIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && handlerInput.requestEnvelope.request.intent.name === 'AMAZON.HelpIntent';
  },
  handle(handlerInput) {
    const speechText = 'Welcome to the Big Mike, your personal fitness coach. Do you want to check if your weight is healthy? just say get my body mass index. Or ask about food nutrition facts. Example, by saying nutrition facts for rice.';
    const repromptText = 'Do you want to check if your weight is healthy? just say get body mass index. Or ask about food nutrition facts. Example, by saying nutrition facts for rice.';

    return handlerInput.responseBuilder
      .speak(speechText)
      .reprompt(speechText)
      .withSimpleCard('Welcome to the Big Mike, your personal fitness coach.', speechText)
      .getResponse();
  },
};

const CancelAndStopIntentHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'IntentRequest'
      && (handlerInput.requestEnvelope.request.intent.name === 'AMAZON.CancelIntent'
        || handlerInput.requestEnvelope.request.intent.name === 'AMAZON.StopIntent');
  },
  handle(handlerInput) {
    const speechText = 'Goodbye!';

    return handlerInput.responseBuilder
      .speak(speechText)
      .withSimpleCard('Big Mike, your personal fitness coach', speechText)
      .getResponse();
  },
};

const SessionEndedRequestHandler = {
  canHandle(handlerInput) {
    return handlerInput.requestEnvelope.request.type === 'SessionEndedRequest';
  },
  handle(handlerInput) {
    console.log(`Session ended with reason: ${handlerInput.requestEnvelope.request.reason}`);

    return handlerInput.responseBuilder.getResponse();
  },
};

const ErrorHandler = {
  canHandle() {
    return true;
  },
  handle(handlerInput, error) {
    console.log(`Error handled: ${error.message}`);

    return handlerInput.responseBuilder
      .speak('Sorry, I can\'t understand the command. Please say again.')
      .reprompt('Sorry, I can\'t understand the command. Please say again.')
      .getResponse();
  },
};

const skillBuilder = Alexa.SkillBuilders.custom();

exports.handler = skillBuilder
  .addRequestHandlers(
    LaunchRequestHandler,
    GetBodyMassIndexIntentHandler,
    GetBodyMassIndexHeightIntentHandler,
    GetBodyMassIndexWeightIntentHandler,
    GetDietForUnderweightIntentHandler,
    GetDietForNormalWeightIntentHandler,
    GetDietForOverweightIntentHandler,
    GetDietForObeseIntentHandler,
    GetGymProgramForUnderweightIntentHandler,
    GetGymProgramForNormalWeightIntentHandler,
    GetGymProgramForOverweightIntentHandler,
    GetGymProgramForObeseIntentHandler,
    GetNutritionFactIntentHandler,
    GetAddCartEnervonCIntentHandler,
    GetAddCartOptimumNutritionWheyProteinIntentHandler,
    GetAddCartCentrumMultivitaminIntentHandler,
    GetAddCartMuscleTechCreatineIntentHandler,
    GetAddCartHydroxycutHardcoreIntentHandler,
    GetAddCartOptimumNutritionLGlutamineIntentHandler,
    GetAddCartAnimalPakMultivitaminIntentHandler,
    GetAddCartSchiekWristWrapsIntentHandler,
    HelpIntentHandler,
    CancelAndStopIntentHandler,
    SessionEndedRequestHandler
  )
  .addErrorHandlers(ErrorHandler)
  .lambda();
