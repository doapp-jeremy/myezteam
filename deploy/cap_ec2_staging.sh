#!/bin/bash

THE_TAG=myeasyteam-production

if [ $# -ne 1 ]
then
	echo "Usage $0 <cap command>"	
	echo "* This will create a ./config/servers.rb file with all RUNNING instances that have tag $THE_TAG"
	echo "* NOTE: ec2 tools MUST be in your path"
	echo "* get ec2 tools here: http://aws.amazon.com/developertools/351"
	echo "* read how to install tools here: http://docs.amazonwebservices.com/AWSEC2/latest/UserGuide/SettingUp_CommandLine.html"
	exit $E_BADARGS
fi

COMMAND="$1"

./deployEC2.sh $THE_TAG
CMD="cap staging $COMMAND"
echo "Running $CMD"
$CMD


