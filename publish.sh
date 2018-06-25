#!/bin/bash

function __publish {
  search='"version": "(.*)"'
  replace='"version": "'${release}'"'
  sed -i -E "s/${search}/${replace}/g" "$1"
}

function __help {
  echo "Usage: $(basename $0) version"
}

if [ -z "$1" ] || [ "$1" = "help" ]; then
  __help
  exit
fi

release=$1

if [ -d ".git" ]; then
  changes=""
  changes=$(git status --porcelain)

  if [ -z "${changes}" ]; then
    __publish "composer.json"
    git add composer.json
    git commit -m "Publish to ${release}"
    git tag -a "${release}"
    git push origin --tags
  else
    echo "Please commit staged files prior to publish"
  fi
else
  __publish "composer.json"
fi