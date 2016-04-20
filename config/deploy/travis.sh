#!/bin/bash

set -exuo pipefail

case "$TRAVIS_BRANCH" in
  develop)
    deploy_environment=staging
    ;;
  master)
    deploy_environment=production
    ;;
  *)
    echo Branch not recognized for deployment
    exit 0
esac

bundle exec cap "$deploy_environment" deploy
