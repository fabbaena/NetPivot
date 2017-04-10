#!/bin/sh

commitId=$(python deploy.py $1)
aws deploy create-deployment --application-name netpivot --deployment-group-name $1 --github-location commitId=$commitId,repository=fabbaena/NetPivot --deployment-config-name CodeDeployDefault.AllAtOnce
