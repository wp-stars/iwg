import React, {useState} from "react"

const ArticleResult = ({ index, post }) => {

    return (
        <div className="container mx-auto mb-5">
            <div className={`w-full article-row flex flex-row ${index % 2 === 0 ? 'even' : 'odd'}`}>
                <div className="img-content w-5/12">
                    <img src={post.featured_image} />
                </div>
                <div className="article-text flex flex-col items-start text-left justify-center w-7/12">
                    <h3 className="mb-5">{post.post_title}</h3>
                    <p className="mb-5">{post.post_author} // {post.post_date} // {post.terms.map(term => term.name).join(' // ')}</p>
                    <div className="mb-10">{post.excerpt}</div>
                    <a href={post.link} className="btn-underline">weiter lesen</a>
                </div>
            </div>
        </div>
    )
}

export default ArticleResult