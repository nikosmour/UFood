const Ziggy = {
    "url": "http:\/\/www.food.mydev",
    "port": null,
    "defaults": {},
    "routes": {
        "sanctum.csrf-cookie": {
            "uri": "sanctum\/csrf-cookie",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "ignition.healthCheck": {
            "uri": "_ignition\/health-check",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "ignition.executeSolution": {
            "uri": "_ignition\/execute-solution",
            "methods": [
                "POST"
            ]
        },
        "ignition.updateConfig": {
            "uri": "_ignition\/update-config",
            "methods": [
                "POST"
            ]
        },
        "coupons.purchase.store": {
            "uri": "api\/coupons\/purchase",
            "methods": [
                "POST"
            ]
        },
        "entryChecking.store": {
            "uri": "api\/entryChecking",
            "methods": [
                "POST"
            ]
        },
        "cardApplication.update": {
            "uri": "api\/cardApplication\/{cardApplication}",
            "methods": [
                "PUT",
                "PATCH"
            ],
            "parameters": [
                "cardApplication"
            ],
            "bindings": {
                "cardApplication": "id"
            }
        },
        "document.index": {
            "uri": "api\/cardApplication\/{cardApplication}\/document",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "cardApplication"
            ],
            "bindings": {
                "cardApplication": "id"
            }
        },
        "document.store": {
            "uri": "api\/cardApplication\/{cardApplication}\/document",
            "methods": [
                "POST"
            ],
            "parameters": [
                "cardApplication"
            ],
            "bindings": {
                "cardApplication": "id"
            }
        },
        "document.show": {
            "uri": "api\/cardApplication\/{cardApplication}\/document\/{document}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "cardApplication",
                "document"
            ],
            "bindings": {
                "cardApplication": "id",
                "document": "id"
            }
        },
        "document.update": {
            "uri": "api\/cardApplication\/{cardApplication}\/document\/{document}",
            "methods": [
                "PUT",
                "PATCH"
            ],
            "parameters": [
                "cardApplication",
                "document"
            ]
        },
        "document.destroy": {
            "uri": "api\/cardApplication\/{cardApplication}\/document\/{document}",
            "methods": [
                "DELETE"
            ],
            "parameters": [
                "cardApplication",
                "document"
            ],
            "bindings": {
                "cardApplication": "id"
            }
        },
        "cardApplication.checking.store": {
            "uri": "api\/cardApplication\/{category}\/checking",
            "methods": [
                "POST"
            ],
            "wheres": {
                "category": "temporary saved|submitted|checking|temporary checked|accepted|rejected|incomplete"
            },
            "parameters": [
                "category"
            ]
        },
        "login": {
            "uri": "login",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "logout": {
            "uri": "logout",
            "methods": [
                "POST"
            ]
        },
        "register": {
            "uri": "register",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "password.request": {
            "uri": "password\/reset",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "password.email": {
            "uri": "password\/email",
            "methods": [
                "POST"
            ]
        },
        "password.reset": {
            "uri": "password\/reset\/{token}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "token"
            ]
        },
        "password.update": {
            "uri": "password\/reset",
            "methods": [
                "POST"
            ]
        },
        "password.confirm": {
            "uri": "password\/confirm",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "home": {
            "uri": "home",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "card.history": {
            "uri": "card\/history",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "coupons.history": {
            "uri": "coupons\/history",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "myInfo": {
            "uri": "myInfo",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "mealPlan.index": {
            "uri": "mealPlan",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "mealPlan.create": {
            "uri": "mealPlan\/create",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "mealPlan.store": {
            "uri": "mealPlan",
            "methods": [
                "POST"
            ]
        },
        "mealPlan.show": {
            "uri": "mealPlan\/{dailyMealPlan}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "dailyMealPlan"
            ],
            "bindings": {
                "dailyMealPlan": "date"
            }
        },
        "mealPlan.edit": {
            "uri": "mealPlan\/{dailyMealPlan}\/edit",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "dailyMealPlan"
            ],
            "bindings": {
                "dailyMealPlan": "date"
            }
        },
        "mealPlan.update": {
            "uri": "mealPlan\/{dailyMealPlan}",
            "methods": [
                "PUT",
                "PATCH"
            ],
            "parameters": [
                "dailyMealPlan"
            ],
            "bindings": {
                "dailyMealPlan": "date"
            }
        },
        "mealPlan.destroy": {
            "uri": "mealPlan\/{dailyMealPlan}",
            "methods": [
                "DELETE"
            ],
            "parameters": [
                "dailyMealPlan"
            ],
            "bindings": {
                "dailyMealPlan": "date"
            }
        },
        "coupons.purchase.create": {
            "uri": "coupons\/purchase\/create",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "entryChecking.create": {
            "uri": "entryChecking\/create",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "coupons.transfer.create": {
            "uri": "coupons\/transfer\/create",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "coupons.transfer.store": {
            "uri": "coupons\/transfer",
            "methods": [
                "POST"
            ]
        },
        "coupons.transfer.show": {
            "uri": "coupons\/transfer\/{transferCoupon}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "transferCoupon"
            ]
        },
        "cardApplication.index": {
            "uri": "cardApplication",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "cardApplication.store": {
            "uri": "cardApplication",
            "methods": [
                "POST"
            ]
        },
        "cardApplication.show": {
            "uri": "cardApplication\/{cardApplication}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "cardApplication"
            ],
            "bindings": {
                "cardApplication": "id"
            }
        },
        "cardApplication.edit": {
            "uri": "cardApplication\/{cardApplication}\/edit",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "cardApplication"
            ],
            "bindings": {
                "cardApplication": "id"
            }
        },
        "cardApplication.checking.index": {
            "uri": "cardApplication\/{category}\/checking",
            "methods": [
                "GET",
                "HEAD"
            ],
            "wheres": {
                "category": "temporary saved|submitted|checking|temporary checked|accepted|rejected|incomplete"
            },
            "parameters": [
                "category"
            ]
        }
    }
};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export {Ziggy};
