# Mongo (legacy) driver version support test

[![Build Status](https://travis-ci.com/gsdevme/mongo-compat-test.svg?branch=master)](https://travis-ci.com/gsdevme/mongo-compat-test)

A simple test of the legacy mongo driver with different versions of the mongo server due to Mongo not listing the compatibility of the legacy driver so it's a guessing game. 

# Running Locally

```
# Stop any other version
make stop

make run mongo=mongo40
```
