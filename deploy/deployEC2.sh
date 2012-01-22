#!/bin/bash

if [ $# -ne 1 ]
then
	echo "Usage $0 ec2-instance-tag"	
	echo "* This will create a ./config/servers.rb file with all RUNNING instances that have specified tag"
	echo "* NOTE: ec2 tools MUST be in your path"
	echo "* get ec2 tools here: http://aws.amazon.com/developertools/351"
	echo "* read how to install tools here: http://docs.amazonwebservices.com/AWSEC2/latest/UserGuide/SettingUp_CommandLine.html"
	exit $E_BADARGS
fi

THE_TAG=$1

INSTANCES_PUBLIC_FQDNS=`ec2-describe-instances --filter tag-value=$THE_TAG --filter instance-state-name=running --hide-tags | grep INSTANCE | cut -f4`

echo "" > ./config/servers.rb
for instance_public_DNS in $INSTANCES_PUBLIC_FQDNS
do
	echo "server \"$instance_public_DNS:49222\", :app" >> ./config/servers.rb
done

