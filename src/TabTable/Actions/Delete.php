<?php

namespace Encore\OrgRbac\TabTable\Actions;

use Encore\Admin\Actions\Response;
use Encore\Admin\Actions\RowAction;
use Encore\Admin\Admin;
use Encore\Admin\Table;
use Encore\OrgRbac\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Delete extends RowAction
{

    protected $resourceUrl;
    protected $backUrl;

    public function __construct($resourceUrl,$backUrl)
    {
        $this->resourceUrl = $resourceUrl;
        $this->backUrl = $backUrl;
    }

    /**
     * @return array|null|string
     */
    public function name()
    {
        return __('admin.delete');
    }

    protected function script()
    {
        return <<<SCRIPT
<script>
function deleteHandle(that)
{
    new Promise(function (resolve,reject) {
        $.ajax({
            method: 'DELETE',
            url: $(that).data('url'),
            data: {
                _token: $.admin.token
            },
            dataType:"json"
        }).done(function (data) {
            $.admin.swal.fire(data.swal);
        }).fail(function(request){
            console.log('系统错误:'+request)
            reject(request);
        });
    }).then(
        window.location = $(that).data('back_url')
    );
}
</script>
SCRIPT;
    }

    public function render()
    {
        $url = $this->resourceUrl.'/'.$this->getKey();
        return sprintf(
            "<a onclick= 'deleteHandle(this)' class='%s' data-url='%s' data-back_url='%s'>%s</a>",
            $this->getElementClass(),
            $url,
            $this->backUrl,
            $this->asColumn ? $this->display($this->row($this->column->getName())) : $this->name()
        ).$this->script();
    }

    public function __toString()
    {
        return $this->render();
    }

}
