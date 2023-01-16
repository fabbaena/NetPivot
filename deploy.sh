#!/bin/sh

commitId=$(curl -s https://api.github.com/repos/fabbaena/NetPivot/branches/$1 | jq -r .commit.sha)
aws deploy create-deployment --application-name netpivot \
    --deployment-group-name $1 \
    --github-location commitId=$commitId,repository=fabbaena/NetPivot \
    --deployment-config-name CodeDeployDefault.AllAtOnce
