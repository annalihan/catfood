<?php 
class Comm_Bigpipe_StreamlineRender extends Comm_Bigpipe_Render
{
    /**
     * 渲染Page的框架
     * @param  Comm_Bigpipe_Pagelet $pagelet [description]
     * @return [type]                   [description]
     */
    protected function renderSkeleton(Comm_Bigpipe_Pagelet $pagelet)
    {
        if ($pagelet->isSkeleton == false)
        {
            return;
        }

        $this->templateEngine->assignValues($pagelet->prepareData());
        $html = $this->templateEngine->render($pagelet->getTemplate());
        $this->pageStart($html);

        $this->flush();
    }

    /**
     * 渲染所有Pagelet
     * @return [type] [description]
     */
    protected function renderPagelet(Comm_Bigpipe_Pagelet $pagelet)
    {
        $html = $this->renderPageletWithJson($pagelet);
        
        if ($html)
        {
            echo $html;
        }

        $this->flush();
    }
}