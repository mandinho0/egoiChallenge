#!/bin/bash

# Waiting broker start
sleep 30

# Create queue
/var/lib/artemis-instance/bin/artemis queue create --name sms-queue --anycast --user artemis --password artemis
