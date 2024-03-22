
const EnumPlugin = {
    install(app) {
        app.config.globalProperties.$enums = {
            "CardDocumentStatusEnum": {
                "SUBMITTED": "submitted",
                "ACCEPTED": "accepted",
                "REJECTED": "rejected",
                "INCOMPLETE": "incomplete"
            },
            "CardStatusEnum": {
                "TEMPORARY_SAVED": "temporary saved",
                "SUBMITTED": "submitted",
                "CHECKING": "checking",
                "TEMPORARY_CHECKED": "temporary checked",
                "ACCEPTED": "accepted",
                "REJECTED": "rejected",
                "INCOMPLETE": "incomplete"
            },
            "MealCategoryEnum": {
                "BREAKFAST": "breakfast",
                "MEAL": "meal",
                "SALAD": "salad",
                "EXTRA": "extra"
            },
            "MealPlanPeriodEnum": {
                "BREAKFAST": "breakfast",
                "LUNCH": "lunch",
                "DINNER": "dinner"
            },
            "UserAbilityEnum": {
                "COUPON_OWNERSHIP": "coupon ownership",
                "COUPON_SELL": "coupon sell",
                "CARD_APPLICATION_CHECK": "card application check",
                "CARD_OWNERSHIP": "card ownership",
                "DAILY_MEAL_PLAN_CREATE": "daily meal plan create",
                "ENTRY_CHECK": "entry check"
            },
            "UserRoleEnum": {
                "STUDENT": "student",
                "RESEARCHER": "researcher",
                "STAFF_COUPON": "staff coupon",
                "STAFF_CARD": "staff card application",
                "STAFF_ENTRY": "staff entry"
            },
            "UserStatusEnum": {
                "UNDERGRADUATE": "undergraduate",
                "POSTGRADUATE": "postgraduate",
                "PHD": "phd",
                "ERASMUS": "erasmus",
                "RESEARCHER": "researcher",
                "STAFF_COUPON": "staff coupon",
                "STAFF_CARD": "staff card application",
                "STAFF_ENTRY": "staff entry"
            }
        };
    }
};

export default EnumPlugin;
