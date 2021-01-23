#!/usr/bin/env bash

DEPLOY_PATH=/home/glutenfr/projects/iam.ivanstanojevic.me
HOST=ivanstanojevic.me
USER=glutenfr
PORT=2233

dep deploy prod

yarn build
ssh ${USER}@${HOST} -p${PORT} "cd ${DEPLOY_PATH} && rm -rf current/public/build"
scp -P ${PORT} -r ./public/build ${USER}@${HOST}:${DEPLOY_PATH}/current/public
