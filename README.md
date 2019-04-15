# NoREC - FastText Model

This page will show how to train a FastText model from the NoReC (The Norwegian Review Corpus) dataset.

The dataset contains annotated reviews in Norwegian ranging from 1 to 6 stars.

The trained model will predict a rating of 1 to 6 starts of any given text.

## Steb-by-step instructions
Download and extract the NoREC dataset
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


## Train FastText Model
Make sure you have fastText installed. 

See instructions here: https://github.com/facebookresearch/fastText#building-fasttext

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
Precision (P@1) is around 0.561

Recall (R@1) can be ignored as this only applies when trainingtexts 
```
N       3517
P@1     0.561
R@1     0.561
```


## Prediction 
Run this command to try the interactive predictor.

Enter some text and it will return a predicted rating between 1 and 6.
```bash
# Predictions
fastText/fasttext predict model_norec.bin -
```