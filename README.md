# NoReC - FastText Model

This page shows step-by-step instructions on how to train a FastText model from the NoReC (The Norwegian Review Corpus) dataset.

The dataset contains annotated reviews in Norwegian ranging from 1 to 6 stars.

The trained model will predict a rating of 1 to 6 starts of any given text.

## Setup
```bash
git clone https://github.com/web64/norec-fasttext.git
cd norec-fasttext
```

## Prepare training data
Download and extract the NoReC dataset
```bash
wget http://folk.uio.no/eivinabe/norec-1.0.1.tar.gz
tar -xvzf norec-1.0.1.tar.gz
tar -xvzf  norec/conllu.tar.gz
```

Convert .conllu files to fastText format
```
php convert.php test
php convert.php dev
php convert.php train
```

This will create the fastText training files:
* norec_test.txt
* norec_dev.txt
* norec_train.txt

The training text files are in this format:
```
__label__6  et episk eventyr et episk eventyr arkitektens læregutt er en storslagen roman ...
__label__1 tåpelig og flau kosebamse-reprise tåpelig og flau kosebamse-reprise komedien...
__label__5 test av mercedes c-klasse c350te...
```
The training texts has been lowercased and cleaned to reduce the number of tokens.

## Train FastText Model
Make sure you have fastText installed. 

See installation instructions here: https://github.com/facebookresearch/fastText#building-fasttext

```bash
# Train Model
fasttext supervised -input norec_train.txt  -output model_norec -epoch 25 -wordNgrams 2 -lr 1.0
```
This will create a model named `model_norec.bin`

See FastText documentation on how to train and optimize models: https://fasttext.cc/docs/en/supervised-tutorial.html


```bash
# Test model
fasttext test model_norec.bin norec_test.txt
```
Precision (P@1) is around 0.561 (this value might change each time the model is trained)

Recall (R@1) can be ignored as this only applies when trainingtexts 
```
N       3517
P@1     0.561
R@1     0.561
```


## Prediction 
Run this command to try the interactive predictor.

Enter some text and it will return a predicted rating between 1 and 6.
```
# Predictions
 >> fastText/fasttext predict model_norec.bin -
 >> sjelden har så mange gode skuespillere gitt så mye for et så bedritent manus og en så flau film
 >> __label__3
```

## See Also
* [NoReC](https://github.com/ltgoslo/norec)
* [NoReC Baseline Models](https://github.com/ltgoslo/norec-baselines)
* [Norwegian NLP Resources](https://github.com/web64/norwegian-nlp-resources)
