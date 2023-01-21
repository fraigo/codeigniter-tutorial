<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session('auth')){
            if ($this->isJson($request)){
                return $this->reject($request);
            }
            return redirect()->to(site_url('/'));
        } else if ($arguments){
            helper('auth');
            if ($arguments[0]=='admin' && is_admin()){
                return $this->reject($request); 
            }
            if ($arguments[0]=='access'){
                $module = @$arguments[1];
                $access = @$arguments[2];
                if (!module_access($module, $access)){
                    return $this->reject($request);
                }
            }
        }
    }

    private function isJson($request){
        $url = current_url(true);
        return $url->getSegment(1) == 'api' || strpos($request->getHeaderLine('Content-Type'), 'application/json') !== false;
    }

    private function reject($request){
        $response = \Config\Services::response();
        if ($this->isJson($request)){
            return $response->setStatusCode(403)->setJSON([
                "success"=>false,
                "errors"=>[
                    "auth" => "Access Forbidden"
                ],
            ]);
        }
        return $response->setStatusCode(403);
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
