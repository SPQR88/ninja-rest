#!/bin/sh

# This script start/stop/restart beanstalk workers by application ID
# Author: Yerlen Zhubangaliyev (yz@yz.kz)
# Usage: ./queue-workers.sh <APP_ID> start|stop|restart

APP=$1
CMD=$2
SOURCE_DIR="$( cd -P "$( dirname "$SOURCE" )" && cd .. && pwd )"
APP_DIR=application
APP_ROOT_DIR=${SOURCE_DIR}/${APP_DIR}/${APP}
APP_BIN_DIR=${APP_ROOT_DIR}/bin/queue
WORKERS=`ls -l $APP_BIN_DIR | awk '{print $9}'`
APP_PID_LOCK_DIR=${SOURCE_DIR}/tmp/run

RC='\e[0;31m'
GC='\e[0;32m'
NC='\e[0m'

start () {
    APP_PID_FILE=${APP_PID_LOCK_DIR}/${APP}.${1}.pid
    APP_LOCK_FILE=${APP_PID_LOCK_DIR}/${APP}.${1}.lock

    if [ ! -e $APP_PID_FILE ]; then
        daemonize -E app=$APP -p $APP_PID_FILE -l $APP_LOCK_FILE -c $APP_ROOT_DIR $APP_BIN_DIR/$1
        echo -e "Worker ${GC}${1}${NC} run with PID: ${GC}`cat ${APP_PID_FILE}`${NC}"
    else
        echo -e "Worker ${RC}${1}${NC} already runs, with PID ${RC}`cat ${APP_PID_FILE}`${NC}"
    fi
}

stop () {
    APP_PID_FILE=${APP_PID_LOCK_DIR}/${APP}.${1}.pid
    APP_LOCK_FILE=${APP_PID_LOCK_DIR}/${APP}.${1}.lock

    if [ -e $APP_PID_FILE ]; then
        kill `cat ${APP_PID_FILE}`
        echo -e "Worker ${GC}${1}${NC} killed, PID: ${GC}`cat ${APP_PID_FILE}`${NC}"
        rm -f $APP_PID_FILE
        rm -f $APP_LOCK_FILE
    else
        echo -e "PID file of worker ${RC}${1}${NC} not found, nothing to kill"
    fi
}

error () {
    echo -e "Usage: ${BASH_SOURCE} ${GC}<APP_ID> start|stop|restart${NC}"
}

if [ ! -d $APP_PID_LOCK_DIR ]; then
    mkdir -p $APP_PID_LOCK_DIR
fi

for WORKER in $WORKERS
do
    if [ -x $APP_BIN_DIR/$WORKER ]; then
        case "$CMD" in
            start) start $WORKER
        ;;
            stop) stop $WORKER
        ;;
            restart) stop $WORKER && start $WORKER
        ;;
            *) error
        ;;
        esac
    fi
done