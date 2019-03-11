#!/usr/bin/env bash

scp src/Table.sql "$1@jepsen.local:dbinit.sh"
ssh "$1@jepsen.local" "psql -f dbinit.sh; rm dbinit.sh"
