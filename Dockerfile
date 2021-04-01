FROM php:8-cli-buster as base

COPY --from=composer /usr/bin/composer /usr/bin/composer

# install required tools
# git for computing diffs
# wget for installation of other tools
# gnupg and g++ for gd extension
# locales for locale-gen command
# apt-utils so package configuartion does not get delayed
# unzip to ommit composer zip packages corruption
# dialog for apt-get to be
# git for computing diffs and for npm to download packages
RUN apt-get update && \
    apt-get install -y \
    wget gnupg g++ locales unzip dialog apt-utils git \
    vim nano mc htop bash-completion \
    && apt-get clean

# enable bash completion
RUN echo "source /etc/bash_completion" >> ~/.bashrc

WORKDIR /var/www/html
