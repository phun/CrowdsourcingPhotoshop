#!/usr/bin/env sh
#
# Copyright 2012 Amazon Technologies, Inc.
# 
# Licensed under the Amazon Software License (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at:
# 
# http://aws.amazon.com/asl
# 
# This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES
# OR CONDITIONS OF ANY KIND, either express or implied. See the
# License for the specific language governing permissions and
# limitations under the License.
 

#get current directory name
DIR_NAME=${PWD##*/}
cd ../..
cd aws/bin
./blockWorker.sh $1 $2 $3 $4 $5 $6 $7 $8 $9 -workerid A2KYQHSSAR531E -reason "continuously spamming the HIT with no input" 
#./blockWorker.sh $1 $2 $3 $4 $5 $6 $7 $8 $9 -workerid A27ZZCI4LRCBU7 -reason "continuously spamming the HIT with no input" 
#./blockWorker.sh $1 $2 $3 $4 $5 $6 $7 $8 $9 -workerid A1AB408CLFAC5D -reason "continuously spamming the HIT with no input" 
#./blockWorker.sh $1 $2 $3 $4 $5 $6 $7 $8 $9 -workerid A2DFFEFL7C3WCQ -reason "continuously spamming the HIT with no input" 
cd ../..
cd real/$DIR_NAME 
