<?php
namespace TYPO3\CMS\Core\Object;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
use TYPO3\Flow\Annotations as Flow;

/**
 * Object Manager
 *
 * @Flow\Scope("singleton")
 * @Flow\Proxy(false)
 */
class ObjectManager extends \TYPO3\Flow\Object\ObjectManager {

	/**
	 * @var \TYPO3\CMS\Core\Core\ClassAliasMap
	 */
	protected $classAliasMap;

	/**
	 * Injector method for a \TYPO3\CMS\Core\Core\ClassAliasMap
	 *
	 * @param \TYPO3\CMS\Core\Core\ClassAliasMap
	 */
	public function injectClassAliasMap(\TYPO3\CMS\Core\Core\ClassAliasMap $classAliasMap) {
		$this->classAliasMap = $classAliasMap;
	}

	/**
	 * @param string $objectName
	 * @return object
	 */
	public function get($objectName) {
		$objectName = $this->classAliasMap->getClassNameForAlias($objectName);
		return parent::get($objectName);
	}

	/**
	 * @param string $objectName
	 * @return int
	 */
	public function getScope($objectName) {
		$objectName = $this->classAliasMap->getClassNameForAlias($objectName);
		return parent::getScope($objectName);
	}

	/**
	 * @param string $caseInsensitiveObjectName
	 * @return mixed
	 */
	public function getCaseSensitiveObjectName($caseInsensitiveObjectName) {
		$caseInsensitiveObjectName = $this->classAliasMap->getClassNameForAlias($caseInsensitiveObjectName);
		return parent::getCaseSensitiveObjectName($caseInsensitiveObjectName);
	}

	/**
	 * @param string $className
	 * @return string
	 */
	public function getObjectNameByClassName($className) {
		$className = $this->classAliasMap->getClassNameForAlias($className);
		return parent::getObjectNameByClassName($className);
	}

	/**
	 * @param string $objectName
	 * @return string
	 */
	public function getClassNameByObjectName($objectName) {
		$objectName = $this->classAliasMap->getClassNameForAlias($objectName);
		return parent::getClassNameByObjectName($objectName);
	}

	/**
	 * @param string $objectName
	 * @return string
	 */
	public function getPackageKeyByObjectName($objectName) {
		$objectName = $this->classAliasMap->getClassNameForAlias($objectName);
		return parent::getPackageKeyByObjectName($objectName);
	}

	/**
	 * @param string $objectName
	 */
	public function forgetInstance($objectName) {
		$objectName = $this->classAliasMap->getClassNameForAlias($objectName);
		parent::forgetInstance($objectName);
	}

	/**
	 * @param string $objectName
	 * @return object
	 */
	protected function buildObjectByFactory($objectName) {
		$objectName = $this->classAliasMap->getClassNameForAlias($objectName);
		return parent::buildObjectByFactory($objectName);
	}

}
?>