<?php

declare(strict_types=1);

// use Exception;

class Permission
{


	const SETUP_AUTHENTICATION = 'setup.authentication';
	const SETUP_DEPARTMENTS = 'setup.departments';
	const SETUP_ROLES = 'setup.roles';
	const SETUP_ROOMS = 'setup.rooms';
	const SETUP_ROOMS_ACL = 'setup.rooms_acl';
	const SETUP_SCHEDULES = 'setup.schedules';
	const SETUP_SESSIONS = 'setup.sessions';
	const SETUP_SETTINGS = 'setup.settings';
	const SETUP_TIMETABLE_WEEKS = 'setup.timetable_weeks';
	const SETUP_USERS = 'setup.users';

	const SYS_BYPASS_MAINTENANCE_MODE = 'system.bypass_maintenance_mode';
	// const SYS_BYPASS_ROOM_ACCESS = 'system.bypass_room_access';
	const SYS_VIEW_ALL_SESSIONS = 'system.view_all_sessions';
	const SYS_EXPORT_BOOKINGS = 'system.export_bookings';

	const ROOM_VIEW = 'room.view';

	const BK_SGL_CREATE = 'book_single.create';
	const BK_SGL_EDIT_OTHER = 'book_single.edit_other_booking';
	const BK_SGL_CANCEL_OTHER = 'book_single.cancel_other_booking';
	const BK_SGL_SET_USER = 'book_single.set_user';
	const BK_SGL_SET_DEPT = 'book_single.set_department';
	const BK_SGL_VIEW_OTHER_NOTES = 'book_single.view_other_notes';
	const BK_SGL_VIEW_OTHER_USERS = 'book_single.view_other_users';

	const BK_RECUR_CREATE = 'book_recur.create';
	const BK_RECUR_EDIT_OTHER = 'book_recur.edit_other_booking';
	const BK_RECUR_CANCEL_OTHER = 'book_recur.cancel_other_booking';
	const BK_RECUR_SET_USER = 'book_recur.set_user';
	const BK_RECUR_SET_DEPT = 'book_recur.set_department';
	const BK_RECUR_VIEW_OTHER_NOTES = 'book_recur.view_other_notes';
	const BK_RECUR_VIEW_OTHER_USERS = 'book_recur.view_other_users';


    /** @var array */
    protected $rules = [];

    /** @var @var CI_Controller */
    protected $CI;

    /** @var object */
    protected $user;

    /** @var array<int, object> */
    protected $local_class_cache = [];

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * Sets the current user
     *
     * @param array $acl the current user who is logged in
     * @return void
     */
    public function set_current_user(?object $user)
    {
        $this->user = $user;
    }

    /**
     * Gets the current user
     *
     * @return array
     */
    public function get_current_user(): object
    {
        return $this->user;
    }

    /**
     * Gets the defined rules
     *
     * @return array
     */
    public function get_rules(): array
    {
        return $this->rules;
    }

    /**
     * Defines a new rule for a permission
     *
     * @param string          $rule     the generic name of the rule
     * @param callable|string $callable either a callable, or the name of the Class->method to call that
     *      will return the allowed permissions.
     *      This will return an array of strings, each string being a permission
     *      e.g. return true; would allow access to this permission
     *           return [ 'create', 'read', 'update', 'delete' ] would allow all permissions
     *           return [ 'read' ] would only allow the read permission.
     *           You define what the callable or method returns for allowed permissions
     *      The callable or method will be passed the following parameters:
     *          - $f3: the \Base instance
     *          - $currentRole: the current role of the logged in user
     * @param bool            $overwrite if true, will overwrite any existing rule with the same name
     * @return void
     */
    public function define_rule(string $rule, $callable_or_class_str, bool $overwrite = false)
    {
        if ($overwrite === false && isset($this->rules[$rule]) === true) {
            throw new \Exception('Rule already defined: ' . $rule);
        }
        $this->rules[$rule] = $callable_or_class_str;
    }

    /**
     * Defines rules based on the public methods of a class
     *
     * @param string $class_name the name of the class to define rules from
     * @return void
     */
    public function define_rules_from_class_methods(string $class_name, int $ttl = 0): void
    {

        $useCache = false;
        if ($ttl > 0) {
            $useCache = true;
            $cacheKey = 'permissions_class_methods_' . $class_name;
            if ($retrieved = $this->CI->cache->get($cacheKey)) {
            	$this->rules = $retrieved;
            	return;
            }

            if ($rules = $this->CI->cache->get($cacheKey) !== false) {
            	$this->rules = $rules;
            	return;
            }
        }

        $reflection = new \ReflectionClass($class_name);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $class_rules = [];
        foreach ($methods as $method) {
            $method_name = $method->getName();
            if (str_starts_with($method_name, '__')) {
                continue;
            }
            $class_rules[$method_name] = $class_name . '->' . $method_name;
        }

        if ($useCache === true) {
        	$this->CI->cache->save($cacheKey, $class_rules, $ttl);
        }

        $this->rules = array_merge($this->rules, $class_rules);
    }

    /**
     * Checks if the current user has permission to perform the action
     *
     * @param string $permission the permission to check. This can be the rule you defined, or a permission.action
     *      e.g. 'video.create' or 'video' depending on how you setup the callback.
     * @param mixed $additional_args any additional arguments to pass to the callback or method.
     * @return bool
     */
    public function can(string $permission, ...$additional_args): bool
    {
        $allowed = false;
        $action = '';
        if (str_contains($permission, '.')) {
            [ $permission, $action ] = explode('.', $permission);
        }

        $permissions_raw = $this->rules[$permission] ?? null;
        if ($permissions_raw === null) {
            throw new Exception('Permission not defined: ' . $permission);
        }

        $executed_permissions = null;

        if (is_callable($permissions_raw) === true) {
            $executed_permissions = $permissions_raw($this->user, ...$additional_args);
        } else {
            if (is_string($permissions_raw) === true) {
                $permissions_raw = explode('->', $permissions_raw);
            }
            [ $className, $methodName ] = $permissions_raw;
            if (isset($this->local_class_cache[$className]) === false) {
                $class = new $className();
                $this->local_class_cache[$className] = $class;
            } else {
                $class = $this->local_class_cache[$className];
            }
            $executed_permissions = $class->$methodName($this->user, ...$additional_args);
        }

        if (is_array($executed_permissions) === true) {
            $allowed = in_array($action, $executed_permissions, true) === true;
        } elseif (is_bool($executed_permissions) === true) {
            $allowed = $executed_permissions;
        }

        return $allowed;
    }

    /**
     * Alias for can. Sometimes it's nice to say has instead of can
     *
     * @param string $permission Permission to check
     * @param mixed $additional_args any additional arguments to pass to the callback or method.
     * @return boolean
     */
    public function has(string $permission, ...$additional_args): bool
    {
        return $this->can($permission, ...$additional_args);
    }

}
