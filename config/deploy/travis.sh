#!/bin/bash

set -exuo pipefail

case "$TRAVIS_BRANCH" in
  develop)
    bundle exec cap staging deploy
    ;;
  master)
    bundle exec cap staging deploy
    ;;
  *)
    echo Branch not recognized for deployment
    ;;
esac
