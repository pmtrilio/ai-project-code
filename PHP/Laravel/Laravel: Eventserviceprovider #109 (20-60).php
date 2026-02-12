            })->setTransactionManagerResolver(function () {
                return app()->bound('db.transactions')
                    ? app('db.transactions')
                    : null;
            });
        });
    }
}
