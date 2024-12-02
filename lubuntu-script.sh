#!/bin/bash
sudo apt-get update
sudo apt-get install docker-compose-plugin
sudo systemctl stop apache2
sudo systemctl stop mysql
sudo systemctl start docker
docker logout
docker compose up -d
