#!/usr/bin/env bash

dep deploy stage
scp -P 2233 -r ./public/build glutenfr@ivanstanojevic.me:/home/glutenfr/projects/dev.ivanstanojevic.me/current/public
