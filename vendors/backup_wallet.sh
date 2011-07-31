#!/bin/bash
# version: 0.5

set -e
set -o nounset

#
# (!) Change these settings!
#

# name/location of the bitcoind executable
BITCOIND="bitcoind"
# insert your gpg-keyid(s) here
GPG_RECIPIENTS='-r 6BBCFF8B -r E04DEC74 -r A86CD3C0'
# path to your wallet.dat
WALLET_PATH="${HOME}/.bitcoin"

wallet_backup_cleanup () {
    # remove backup
    shred -xu "${MY_TMPDIR}"/*
    rm -rf "${MY_TMPDIR}"
}

# create temporary file
MY_TMPDIR="$(mktemp -d)"
MY_LOCKFILE="${MY_TMPDIR}/.flock.$(hostname).wallet_backup.asc"

trap "wallet_backup_cleanup" INT TERM EXIT

(
    # make sure only one instance is running
    flock -s 200

    # backup wallet
    rm -f "${MY_TMPDIR}/wallet.dat"
    ${BITCOIND} backupwallet "${MY_TMPDIR}" || { cp -v ${WALLET_PATH}/wallet.dat "${MY_TMPDIR}"; }
    BACKUP_FILE="wallet.dat"

    # encrypt wallet
    gpg ${GPG_RECIPIENTS} --default-recipient-self --trust-model=always --yes --batch --armor --encrypt-files "${MY_TMPDIR}/wallet.dat"
    BACKUP_FILE="wallet.dat.asc"

    # rename
    NEW_FILENAME="$(date +%Y-%m-%dT%H:%M:%S)_$(hostname).${BACKUP_FILE}"
    mv -f "${MY_TMPDIR}/wallet.dat.asc" "${MY_TMPDIR}/${NEW_FILENAME}"
    BACKUP_FILE="${NEW_FILENAME}"

    # compress
    xz -e "${MY_TMPDIR}/${NEW_FILENAME}"
    BACKUP_FILE="${NEW_FILENAME}.xz"

    # 
    # copy away... 
    # (!) insert any commands to copy the wallet to a safe place
    #
    #rsync -vaP "${MY_TMPDIR}/${NEW_FILENAME}" someuser@somehost.tld
    #s3cmd put --acl-private "${MY_TMPDIR}/${BACKUP_FILE}" s3://some-s3-bucket-you-own
) 200>"${MY_LOCKFILE}"
