/**
 * MAGESTORE_NAMESPACE_MANAGER is namespace
 * @type {object}
 */
var MAGESTORE_NAMESPACE_MANAGER = MAGESTORE_NAMESPACE_MANAGER || {
    registerNamespace: function(_namepsace) {
        var checkExists = false;
        var pathNamespace = "";
        var spaces = _namepsace.split(".");
        for (var i = 0; i < spaces.length; i++) {
            if (pathNamespace != "") {
                pathNamespace += ".";
            }
            pathNamespace += spaces[i];
            checkExists = this.existsNamespace(pathNamespace);
            if (!checkExists) {
                this.createNamespace(pathNamespace);
            }
        }
        if (checkExists) {
            throw "Namespace: " + _namepsace + " is already defined.";
        }
    },
    createNamespace: function(_pathNamespace) {
        eval("window." + _pathNamespace + " = {};");
    },
    existsNamespace: function(_pathNamespace) {
        eval("var NE = false; try{if(" + _pathNamespace + "){NE = true;}else{NE = false;}}catch(err){NE=false;}");
        return NE;
    }
}