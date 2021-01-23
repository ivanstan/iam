#!/usr/bin/env bash

dep deploy prod
deployer/recipes
scp -P 2233 -r ./public/build glutenfr@ivanstanojevic.me:/home/glutenfr/projects/iam.ivanstanojevic.me/current/public
